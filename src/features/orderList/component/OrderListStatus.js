import api from '@app/api';
import {appDimensions, colors, globalStyles} from '@app/assets';
import {useRoute} from '@react-navigation/native';
import _ from 'lodash';
import React, {useEffect, useRef, useState} from 'react';
import {StyleSheet, FlatList, View, TouchableOpacity} from 'react-native';
import {Divider, Text} from 'react-native-elements';
import {useQuery} from 'react-query';

const fetch = async () => {
  return await api.getListOrderStatus();
};

const TabItem = React.memo(
  ({tab, onPress, selected}) => {
    return (
      <TouchableOpacity
        onPress={onPress}
        style={[styles.container, selected ? styles.selected : null]}>
        <Text style={{fontSize: 14, textAlign: 'center', color: '#3b4859'}}>
          {tab.name}
        </Text>
      </TouchableOpacity>
    );
  },
  (prev, next) =>
    _.isEqual(prev.tab, next.tab) && prev.selected === next.selected,
);
const component = ({onPress}) => {
  const route = useRoute();
  const flatRef = useRef();
  const [tabSelected, setTabSelected] = useState('0');
  const [listTabs, setListTabs] = useState([{id: '0', name: 'Tất cả'}]);
  const {data} = useQuery(['getListStatusOrder'], fetch);

  useEffect(() => {
    if (data) {
      setListTabs([{id: '0', name: 'Tất cả'}, ...data]);
      if (route.params?.id) {
        setTabSelected(route.params?.id);
        onPress(route.params?.id);
        setTimeout(() => {
          if (flatRef && flatRef.current) {
            const indexTab = _.findIndex(
              data,
              dt => dt.id === route.params?.id,
            );
            if (indexTab >= 0) {
              flatRef.current.scrollToIndex({animated: true, index: indexTab});
            }
          }
        }, 500);
      }
    }
  }, [data]);

  function onSelect(id) {
    setTabSelected(id);
    onPress(id);
  }
  return (
    <View>
      <FlatList
        ref={flatRef}
        horizontal
        data={listTabs}
        showsHorizontalScrollIndicator={false}
        key="order_tabs"
        keyExtractor={item => `order_tabs${item.id}`}
        renderItem={({item}) => (
          <TabItem
            tab={item}
            selected={item.id === tabSelected}
            onPress={() => onSelect(item.id)}
          />
        )}
      />
      <Divider />
    </View>
  );
};
export const OrderListStatus = React.memo(component, () => true);

const styles = StyleSheet.create({
  container: {
    height: 45,
    justifyContent: 'center',
    paddingHorizontal: 10,
    minWidth: appDimensions.width / 4,
  },
  title: {
    ...globalStyles.text,
    textAlign: 'center',
    color: '#3b4859',
  },
  icon: {
    paddingHorizontal: 6,
  },
  selected: {
    borderBottomColor: colors.primary,
    borderBottomWidth: 1,
  },
});
