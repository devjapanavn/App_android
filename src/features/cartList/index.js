import {
  FlatList,
  InteractionManager,
  StatusBar,
  StyleSheet,
  View,
} from 'react-native';
import React, {useCallback, useEffect, useState} from 'react';
import {useDispatch, useSelector} from 'react-redux';

import {CartItem} from './component/CartItem';
import {Divider} from 'react-native-elements';
import {EmptyCart} from './component';
import {FooterCart} from './component/FooterCart';
import {ROUTES} from '@app/constants';
import Spinner from 'react-native-spinkit';
import _ from 'lodash';
import api from '@app/api';
import {colors} from '@app/assets';
import {getTotalCart} from '@app/store/auth/services';
import {navigateRoute} from '@app/route';
import {useFocusEffect} from '@react-navigation/native';
import {useQuery} from 'react-query';

const Screen = props => {
  const [onReady, setOnReady] = useState(false);
  const [carts, setCarts] = useState([]);
  const [suggestionList, setSuggestionList] = useState([]);
  const {user} = useSelector(state => ({
    user: state.auth.user,
  }));
  const dispatch = useDispatch();

  useEffect(() => {
    StatusBar.setBackgroundColor(colors.darkPrimary);
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    dispatch(getTotalCart(user?.id));
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  useFocusEffect(
    React.useCallback(() => {
      refetch();
    }, []),
  );

  const fetchgetCartList = async () => {
    return await api.getListCart(user?.id);
  };

  const {status, data, error, refetch, isFetching, isLoading} = useQuery(
    ['getCartList', {id: user?.id}],
    fetchgetCartList,
    {
      cacheTime: 0,
      staleTime: 0,
      enabled: onReady,
      refetchOnWindowFocus: 'always',
    },
  );

  useEffect(() => {
    // setCarts([]);
    // setSuggestionList([]);
    if (data && !isFetching) {
      if (data.length) {
        setCarts(data);
      } else if (data.suggestion) {
        setSuggestionList(data.suggestion);
      }
    }
  }, [data, isFetching]);

  const onRemoveItemCart = useCallback(
    cart => {
      if (!_.isEmpty(cart)) {
        const newsData = _.filter(carts, c => c.id !== cart.id);
        setCarts(newsData);
      } else {
        setCarts([]);
        // setTimeout(() => {
        refetch();
        // }, 500);
      }
    },
    [carts],
  );

  if (!onReady || isLoading) {
    return (
      <View
        style={{
          justifyContent: 'center',
          alignItems: 'center',
          margin: 10,
          flex: 1,
        }}>
        <Spinner type="Circle" color={colors.primary} size={40} />
      </View>
    );
  }
  return (
    <View style={{flex: 1}}>
      <FlatList
        style={styles.box}
        data={carts}
        horizontal={false}
        ListEmptyComponent={() => <EmptyCart suggestionList={suggestionList} />}
        contentContainerStyle={{flexGrow: 1}}
        ItemSeparatorComponent={() => <Divider style={{margin: 10}} />}
        renderItem={({item}) => (
          <CartItem cart={item} onRemoveItem={onRemoveItemCart} />
        )}
      />
      {carts && carts.length > 0 ? <FooterCart /> : null}
    </View>
  );
};

const styles = StyleSheet.create({
  box: {
    flex: 1,
    backgroundColor: '#fff',
  },
  footerDiscount: {
    color: '#0F83FF',
    fontSize: 13,
    lineHeight: 18,
    padding: 10,
  },
  buttonSubmit: {
    width: 150,
    height: 48,
    borderRadius: 4,
    backgroundColor: '#dc0000',
  },
});

export const CartListScreen = Screen;
