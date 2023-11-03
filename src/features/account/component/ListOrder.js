import api from '@app/api';
import { appDimensions } from '@app/assets';
import { ROUTES } from '@app/constants';
import { navigateRoute } from '@app/route';
import { stringHelper } from '@app/utils';
import { useFocusEffect } from '@react-navigation/native';
import _ from 'lodash';
import React, { useEffect } from 'react';
import { StyleSheet, TouchableOpacity, View } from 'react-native';
import { Text } from 'react-native-elements';
import Animated, {
  interpolate,
  useAnimatedStyle,
  useSharedValue,
  withSpring,
} from 'react-native-reanimated';
import { useQuery } from 'react-query';
import { useSelector } from 'react-redux';

const COLOR_ORDERS = ['#ffa200', '#2bd600', '#2367ff'];
const BACKGROUND_ORDERS = ['#fff9f0', '#f3fff0', '#f5f9ff'];
const fetch = async (userId, showHome = 1) => {
  return await api.getTotalOrderStatus(userId, showHome);
};

const component = () => {
  const { user } = useSelector(state => ({
    user: state.auth.user,
  }));
  const toggleHeight = useSharedValue(0);

  const { status, data, error, refetch } = useQuery(
    ['getTotalOrderStatus', { userId: user?.id }],
    () => fetch(user?.id),
    {
      enabled: !_.isEmpty(user),
    },
  );

  useFocusEffect(React.useCallback(() => {
    refetch()
    return () => {
    };
  }, [user?.id]))


  useEffect(() => {
    toggleHeight.value = withSpring(1);
  }, [data]);

  const _renderOrder = ({ item, index }) => {
    return (
      <View style={styles.wrapItem}>
        <TouchableOpacity
          style={[
            styles.itemContainer,
            {
              backgroundColor: BACKGROUND_ORDERS[index],
              borderColor: COLOR_ORDERS[index],
            },
          ]}
          onPress={() =>
            navigateRoute(ROUTES.ORDER_LIST, { id: item.id }, false, true)
          }>
          <Text style={[styles.itemCount, { color: COLOR_ORDERS[index] }]}>
            {stringHelper.formatMoney(item.total_order)}
          </Text>
          <Text style={[styles.itemName, { color: COLOR_ORDERS[index] }]}>
            {item.name}
          </Text>
        </TouchableOpacity>
      </View>
    );
  };

  const toggleStyle = useAnimatedStyle(() => {
    const heightToggle = interpolate(toggleHeight.value, [0, 1], [0, 88]);
    return {
      height: heightToggle,
    };
  });

  return (
    <Animated.FlatList
      horizontal
      style={[styles.listContainer, toggleStyle]}
      showsHorizontalScrollIndicator={false}
      data={data}
      key="account_listorder"
      keyExtractor={item => `account_listorder_${item.id}`}
      renderItem={_renderOrder}
      ItemSeparatorComponent={() => <View style={{ width: 20 }} />}
    />
  );
};
export const ListOrder = React.memo(component, () => true);

const styles = StyleSheet.create({
  container: { backgroundColor: '#dc0000' },
  avatarBorder: {
    borderColor: '#fff',
    borderWidth: 1,
  },
  text: {
    color: '#fff',
  },
  listContainer: {
    padding: 10,
    marginHorizontal: 5,
  },
  itemContainer: {
    borderWidth: 1,
    borderRadius: 8,
    paddingHorizontal: 8,
    paddingVertical: 10,
    alignItems: 'center',
    elevation: 2,
    marginHorizontal: 8,
    minWidth: 100,
  },

  itemCount: {
    fontSize: 14,
  },
  itemName: {
    fontSize: 12,
    fontWeight: '500',
  },
});
