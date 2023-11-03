import api from '@app/api';
import {colors, spacing} from '@app/assets';
import {ROUTES} from '@app/constants';
import {gobackRoute, navigateRoute} from '@app/route';
import {useRoute} from '@react-navigation/native';
import _, {round} from 'lodash';
import React, {useEffect, useState} from 'react';
import {
  StyleSheet,
  InteractionManager,
  StatusBar,
  FlatList,
  View,
} from 'react-native';
import {BottomSheet, Button, Divider, ListItem} from 'react-native-elements';
import {SafeAreaView} from 'react-native-safe-area-context';
import Spinner from 'react-native-spinkit';
import {useQuery} from 'react-query';
import {useSelector} from 'react-redux';
import {AddressItem} from './component';

const fetch = async userId => {
  return api.getListAddress(userId);
};
const Screen = props => {
  const route = useRoute();
  const [onReady, setOnReady] = useState(false);
  const [loadingDefault, setLoadingDefault] = useState(false);
  const [isDeleting, setDeleting] = useState(false);
  const [list, setList] = useState([]);
  const [optionsModal, setOptionsModal] = useState({
    visible: false,
    current: null,
  });
  const {user} = useSelector(state => ({
    user: state.auth.user,
  }));

  const {status, data, error, refetch, isLoading} = useQuery(
    ['getListAddress', {userId: user?.id}],
    () => fetch(user?.id),
    {enabled: onReady, cacheTime: 0, staleTime: 0},
  );

  useEffect(() => {
    if (data && data.length > 0) {
      setList(data);
    }
  }, [data]);

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  function onSelectedAddress(address) {
    if (route.params?.onSelect) {
      gobackRoute();
      route.params?.onSelect(address);
    } else {
      setOptionsModal({visible: true, current: address});
    }
  }

  async function onSetDefault() {
    if (optionsModal.current && optionsModal.current.id) {
      try {
        setLoadingDefault(true);
        const res = await api.setDefaultAddress(
          user?.id,
          optionsModal.current?.id,
        );
        if (res) {
          const newsList = _.map(list, dt => {
            if (dt.id === optionsModal.current.id) {
              dt.default = '1';
            } else {
              dt.default = '0';
            }
            return dt;
          });
          setList(newsList);
          setOptionsModal({visible: false, current: null});
        }
        setLoadingDefault(false);
      } catch (error) {
        setLoadingDefault(false);
      }
    }
  }

  function onAddOrEdit() {
    let address = null;
    if (optionsModal.current) {
      address = {...optionsModal.current};
    }
    setOptionsModal({visible: false, current: null});
    setTimeout(() => {
      navigateRoute(ROUTES.ADDRESS_ADD, {
        ...address,
        onRefresh: refetch,
        onSelect: selectedAddress => {
          if (route.params?.onSelect) {
            gobackRoute();
            route.params?.onSelect(selectedAddress);
          }
        },
      });
    }, 300);
  }

  async function onDelete() {
    if (optionsModal.current && optionsModal.current.id) {
      try {
        setDeleting(true);
        const res = await api.removeAddress(user?.id, optionsModal.current?.id);
        setDeleting(false);
        if (res) {
          const newsList = _.filter(
            list,
            dt => dt.id !== optionsModal.current.id,
          );
          setList(newsList);
          setOptionsModal({visible: false, current: null});
        }
      } catch (error) {
        setDeleting(false);
      }
    }
  }

  const renderFooter = () => {
    return (
      <View>
        <Divider />
        <Button
          onPress={() => onAddOrEdit()}
          title="Thêm địa chỉ mới"
          containerStyle={styles.buttonContainer}
          titleStyle={{fontSize: 17}}
          buttonStyle={{backgroundColor: '#2367ff', borderRadius: 4}}
        />
      </View>
    );
  };

  if (!onReady && isLoading) {
    return (
      <View
        style={{
          justifyContent: 'center',
          alignItems: 'center',
          margin: spacing.large,
          flex: 1,
        }}>
        <Spinner type="Circle" color={colors.primary} size={40} />
      </View>
    );
  }

  return (
    <SafeAreaView style={styles.box}>
      <StatusBar barStyle="light-content" backgroundColor="#dc0000" />
      <FlatList
        style={{flex: 1}}
        data={list}
        key="address_list"
        ItemSeparatorComponent={() => <View style={{height: 10}}></View>}
        keyExtractor={item => `address_list_${item.id}`}
        renderItem={({item}) => (
          <AddressItem
            data={item}
            onEdit={() =>
              navigateRoute(ROUTES.ADDRESS_ADD, {
                ...item,
                onRefresh: refetch,
              })
            }
            onPress={() => onSelectedAddress(item)}
            isDefault={item.default || '0'}
          />
        )}
      />
      {renderFooter()}

      <BottomSheet
        isVisible={optionsModal.visible}
        containerStyle={{
          backgroundColor: 'rgba(0, 0, 0, 0.75)',
        }}>
        <View
          style={{
            margin: 10,
            borderRadius: 8,
            overflow: 'hidden',
            backgroundColor: '#fff',
          }}>
          {isDeleting ? (
            <View style={styles.deletingOverlay}>
              <Spinner type="Circle" color={colors.white} size={40} />
            </View>
          ) : null}
          <ListItem bottomDivider onPress={() => onSetDefault()}>
            <ListItem.Content style={{alignItems: 'center'}}>
              <ListItem.Title>Đặt làm mặc định</ListItem.Title>
            </ListItem.Content>
            <Spinner
              type="FadingCircleAlt"
              color={colors.primary}
              size={20}
              isVisible={loadingDefault}
              style={{position: 'absolute', top: 15, right: 15}}
            />
          </ListItem>
          <ListItem bottomDivider onPress={() => onAddOrEdit()}>
            <ListItem.Content style={{alignItems: 'center'}}>
              <ListItem.Title>Chỉnh sửa</ListItem.Title>
            </ListItem.Content>
          </ListItem>
          <ListItem bottomDivider onPress={() => onDelete()}>
            <ListItem.Content style={{alignItems: 'center'}}>
              <ListItem.Title style={{color: '#dc0000'}}>Xóa</ListItem.Title>
            </ListItem.Content>
          </ListItem>
          <ListItem
            bottomDivider
            onPress={() => setOptionsModal({visible: false, current: null})}>
            <ListItem.Content style={{alignItems: 'center'}}>
              <ListItem.Title style={{color: '#2367ff'}}>Hủy</ListItem.Title>
            </ListItem.Content>
          </ListItem>
        </View>
      </BottomSheet>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  box: {
    flex: 1,
    backgroundColor: '#fff',
  },
  title: {
    fontSize: 16,
  },
  buttonContainer: {
    margin: 10,
  },
  deletingOverlay: {
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: 10,
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: 'rgba(0,0,0,0.5)',
  },
});

export const AddressListScreen = Screen;
