import { ROUTES } from '@app/constants';
import { navigateRoute, resetAndNavigateRoute, resetRoute } from '@app/route';
import { getProfile, onLogout } from '@app/store/auth/services';
import React, { useCallback, useEffect, useState } from 'react';
import {
  StyleSheet,
  InteractionManager,
  View,
  StatusBar,
  Alert,
} from 'react-native';
import { Avatar, Divider, Icon, ListItem, Text } from 'react-native-elements';
import { ScrollView } from 'react-native-gesture-handler';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useDispatch, useSelector } from 'react-redux';
import { HeaderAccount, ListOrder } from './component';
import messaging from '@react-native-firebase/messaging';
import api from '@app/api';
import { useFocusEffect } from '@react-navigation/native';
import { useQuery } from 'react-query';

const Screen = props => {
  const [onReady, setOnReady] = useState(false);
  const { user, id_static_ve_chung_toi } = useSelector(state => ({
    user: state.auth.user,
    id_static_ve_chung_toi: state.root.id_static_ve_chung_toi
  }));

  const dispatch = useDispatch();
  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  useFocusEffect(React.useCallback(() => {
    let isActive = true;
    const fetchUser = async () => {
      try {
        dispatch(getProfile(user?.id))
      } catch (e) {
      }
    };

    fetchUser();
    return () => {
      isActive = false;
    };
  }, [user?.id]))

  const onLogOut = () => {
    Alert.alert(
      'Xác nhận',
      'Đăng xuất tài khoản',
      [{ text: 'Xác nhận', onPress: logout }, { text: 'Đóng' }],
      {
        cancelable: true,
      },
    );
  };

  function logout() {
    if (user && user.id) {
      dispatch(onLogout(user.id)).then(async res => {
        const authorizationStatus = await messaging().requestPermission();
        if (authorizationStatus) {
          const token = await messaging().getToken();
          await api.pushToken(null, token)
        }
        resetRoute(ROUTES.MAIN_TABS);
      });
    }
  }

  return (
    <SafeAreaView style={styles.box}>
      <StatusBar barStyle="light-content" backgroundColor="#dc0000" />
      <HeaderAccount />
      {onReady ? (
        <ScrollView style={{ paddingHorizontal: 10 }}>
          <ListItem
            bottomDivider
            onPress={() => navigateRoute(ROUTES.ADDRESS_LIST)}>
            <Icon name="location" type="ionicon" color="rgb(35, 103, 255)" />
            <ListItem.Content>
              <ListItem.Title style={styles.title}>Sổ địa chỉ</ListItem.Title>
            </ListItem.Content>
            <ListItem.Chevron
              name="chevron-forward"
              type="ionicon"
              color="rgb(138, 138, 143)"
            />
          </ListItem>
          <ListItem
            bottomDivider
            onPress={() => navigateRoute(ROUTES.PROFILE, false, false, true)}>
            <Icon
              name="ios-person-circle-outline"
              type="ionicon"
              color="rgb(220, 0, 0)"
            />
            <ListItem.Content>
              <ListItem.Title style={styles.title}>Tài khoản</ListItem.Title>
            </ListItem.Content>
            <ListItem.Chevron
              name="chevron-forward"
              type="ionicon"
              color="rgb(138, 138, 143)"
            />
          </ListItem>
          <ListItem onPress={() => navigateRoute(ROUTES.ORDER_LIST)}>
            <Icon
              name="history"
              type="material-community"
              color="rgb(240, 136, 0)"
            />
            <ListItem.Content>
              <ListItem.Title style={styles.title}>Đơn hàng</ListItem.Title>
            </ListItem.Content>
            <ListItem.Subtitle>Xem lịch sử</ListItem.Subtitle>
            <ListItem.Chevron
              name="chevron-forward"
              type="ionicon"
              color="rgb(138, 138, 143)"
            />
          </ListItem>
          <ListOrder />
          <Divider />
          <ListItem
            bottomDivider
            onPress={() => navigateRoute(ROUTES.STATIC_BLOG, { id: id_static_ve_chung_toi })}>
            <Icon
              name="information"
              type="material-community"
              color="rgb(35, 103, 255)"
            />
            <ListItem.Content>
              <ListItem.Title style={styles.title}>Về chúng tôi</ListItem.Title>
            </ListItem.Content>
            <ListItem.Chevron
              name="chevron-forward"
              type="ionicon"
              color="rgb(138, 138, 143)"
            />
          </ListItem>
          <ListItem bottomDivider onPress={() => navigateRoute(ROUTES.LIST_STATIC_BLOG)}>
            <Icon name="policy" type="material" color="rgb(200, 0, 0)" />
            <ListItem.Content>
              <ListItem.Title style={styles.title}>
                Điều khoản và chính sách
              </ListItem.Title>
            </ListItem.Content>
            <ListItem.Chevron
              name="chevron-forward"
              type="ionicon"
              color="rgb(138, 138, 143)"
            />
          </ListItem>
          <ListItem
            bottomDivider
            onPress={() => navigateRoute(ROUTES.STATIC_BLOG, { id: 12 })}>
            <Icon
              name="question-answer"
              type="material"
              color="rgb(255, 162, 0)"
            />
            <ListItem.Content>
              <ListItem.Title style={styles.title}>
                Câu hỏi thường gặp
              </ListItem.Title>
            </ListItem.Content>
            <ListItem.Chevron
              name="chevron-forward"
              type="ionicon"
              color="rgb(138, 138, 143)"
            />
          </ListItem>
          <ListItem
            bottomDivider
            onPress={() => navigateRoute(ROUTES.CHANGE_PASSWORD)}>
            <Icon
              name="lock"
              type="material-community"
              color="rgb(138, 138, 143)"
            />
            <ListItem.Content>
              <ListItem.Title style={styles.title}>Đổi mật khẩu</ListItem.Title>
            </ListItem.Content>
            <ListItem.Chevron
              name="chevron-forward"
              type="ionicon"
              color="rgb(138, 138, 143)"
            />
          </ListItem>
          <ListItem bottomDivider onPress={onLogOut}>
            <Icon
              name="logout"
              type="material-community"
              color="rgb(138, 138, 143)"
            />
            <ListItem.Content>
              <ListItem.Title style={styles.title}>Đăng xuất</ListItem.Title>
            </ListItem.Content>
            <ListItem.Chevron
              name="chevron-forward"
              type="ionicon"
              color="rgb(138, 138, 143)"
            />
          </ListItem>
        </ScrollView>
      ) : null}
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
});

export const AccountScreen = Screen;
