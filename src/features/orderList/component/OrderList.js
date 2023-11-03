import React, {useCallback, useEffect, useState} from 'react';
import {FlatList, RefreshControl, View} from 'react-native';
import {Searchbar} from 'react-native-paper';
import {OrderItem} from './orderItem';
import _ from 'lodash';
import api from '@app/api';
import {useQuery} from 'react-query';
import {colors} from '@app/assets';
import {useSelector} from 'react-redux';

const fetch = async (userId, tab = '0', page = 1, code = '') => {
  return await api.getListOrder(userId, tab, page, code);
};

export const OrderList = React.memo(
  ({currentTab}) => {
    const {user} = useSelector(state => ({
      user: state.auth.user,
    }));
    const [enableFetch, setEnableFetch] = useState(true);
    const [filter, setFilter] = useState({page: 1, code: ''});
    const [list, setList] = useState([]);

    const handleDebounceSearch = useCallback(
      _.debounce(value => setEnableFetch(true), 400),
      [],
    );

    useEffect(() => {
      setList([]);
      setFilter(prev => ({...prev, page: 1}));
    }, [currentTab]);

    useEffect(() => {
      handleDebounceSearch();
    }, [filter.code]);

    const {data, isLoading, refetch} = useQuery(
      [
        'getListOrder',
        {member_id: user?.id, status_cart: currentTab, ...filter},
      ],
      () => fetch(user?.id, currentTab, filter.page, filter.code),
      {enabled: enableFetch, cacheTime: 0},
    );

    useEffect(() => {
      if (data) {
        if (filter.page === 1) {
          setList(data);
        } else {
          setList(prev => [...prev, ...data]);
        }
      } else if (filter.page === 1) {
        setList([]);
      }
    }, [data]);

    function onLoadMore() {
      if (filter.page * 20 === list.length) {
        setFilter(prev => ({...prev, page: prev.page + 1}));
      }
    }
    return (
      <>
        <Searchbar
          placeholder="Tìm kiếm theo mã vận đơn ..."
          style={{margin: 10, height: 40}}
          inputStyle={{fontSize: 13}}
          onChangeText={text => {
            setEnableFetch(false);
            setFilter({code: text, page: 1});
          }}
        />
        <FlatList
          refreshControl={
            <RefreshControl
              tintColor={colors.primary}
              refreshing={isLoading && filter.page === 1}
              onRefresh={() => {
                setFilter(prev => ({...prev, page: 1}));
                refetch();
              }}
            />
          }
          removeClippedSubviews={true}
          style={{flex: 1}}
          showsVerticalScrollIndicator={false}
          data={list}
          key={`order_list_${currentTab}`}
          ItemSeparatorComponent={() => <View style={{height: 10}}></View>}
          keyExtractor={item => `order_list_${currentTab}_${item.id}`}
          onEndReachedThreshold={0.5}
          onEndReached={onLoadMore}
          getItemLayout={(data, index) => ({
            length: 190,
            offset: 190 * index,
            index,
          })}
          renderItem={({item}) => <OrderItem data={item} />}
        />
      </>
    );
  },
  (prev, next) =>
    prev.userId === next.userId && prev.currentTab === next.currentTab,
);
