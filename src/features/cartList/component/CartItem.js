import {
  Alert,
  Pressable,
  StyleSheet,
  TouchableOpacity,
  View,
  FlatList
} from 'react-native';
import {Button, Chip, Text} from 'react-native-elements';
import React, {useCallback, useState} from 'react';
import {useDispatch, useSelector} from 'react-redux';

import {CountNumber} from '../../../components/CountNumber';
import FastImage from 'react-native-fast-image';
import PropTypes from 'prop-types';
import {GLOBAL_FUNC, ROUTES} from '@app/constants';
import api from '@app/api';
import {getTotalCart} from '@app/store/auth/services';
import {globalStyles} from '@app/assets';
import {iOSColors} from 'react-native-typography';
import {navigateRoute} from '@app/route';
import {stringHelper} from '@app/utils';

const CartItemComponent = ({cart, onRemoveItem}) => {
  const [quantity, setQuality] = useState(cart.qty);
  const [loading, setLoading] = useState(false);
  const {user} = useSelector(state => ({
    user: state.auth.user,
  }));

  const dispatch = useDispatch();
  const handleOnPressItem = useCallback(() => {
    let productId = cart.id_product;
    if (cart.id_product_main && cart.id_product_main.length > 0) {
      productId = cart.id_product_main;
    }
    navigateRoute(
      ROUTES.DETAIL_PRODUCT,
      {id: productId},
      `product_detail_${productId}`,
    );
  }, []);

  function onRemoveItemCart() {
    Alert.alert('Xóa sản phẩm', 'Bạn có chắc chắn bỏ sản phẩm này.', [
      {
        text: 'Xóa',
        onPress: () => remove(),
      },
      {text: 'Đóng'},
    ]);
  }

  async function remove() {
    if (cart) {
      setLoading(true);
      try {
        const res = await api.removeCartItem(user?.id, cart.id);
        // console.log('removeCartIte', res);
        dispatch(getTotalCart(user?.id));
        setLoading(false);
        if (onRemoveItem) {
          onRemoveItem(res);
        }
      } catch (error) {
        setLoading(false);
      }
    }
  }

  async function onChangeQuality(val) {
    setQuality(val);
    if (cart) {
      try {
        await api.updateCartItem(user?.id, cart.id, val);
        setTimeout(() => {
          dispatch(getTotalCart(user?.id));
        }, 300);
      } catch (error) {
        console.log('error', error);
      }
    }
  }
  const _renderPrice = () => {
    cart = GLOBAL_FUNC.filterPrice(cart);
    // console.log(cart);
    if (cart.price_goc) {
      return (
        <View style={{flexDirection: 'column'}}>
          <Text style={styles.priceBefore}>
            {stringHelper.formatMoney(cart.price_goc)} đ
          </Text>
          <Text style={styles.price}>
            {stringHelper.formatMoney(cart.price)} đ
          </Text>
        </View>
      );
    } else {
      return (
        <Text style={styles.price}>
          {stringHelper.formatMoney(cart.price)} đ
        </Text>
      );
    }
  };

  return (
    <View>
      <View style={styles.container}>
        <TouchableOpacity onPress={handleOnPressItem}>
          <FastImage
            source={{uri: cart.images, priority: 'normal'}}
            style={styles.imageProduct}
          />
        </TouchableOpacity>
        <View style={styles.infoContainer}>
          <TouchableOpacity onPress={handleOnPressItem}>
            <Text style={styles.title}>{cart.name}</Text>
            {cart.combo &&
        cart.combo.length > 0 ?( <Text
          style={[styles.gifText, {backgroundColor: iOSColors.orange,marginTop: 5}]}>
          Combo
        </Text>) : null}
            <View style={{flexDirection: 'row', marginTop: 8}}>
              {_renderPrice()}
            </View>
          </TouchableOpacity>
          <View style={styles.cartAction}>
            <Button
              loading={loading}
              type="clear"
              title="Xóa"
              titleStyle={{fontSize: 13, color: '#0F83FF', fontWeight: '300'}}
              onPress={onRemoveItemCart}
            />
            <CountNumber
              noTitle={true}
              value={quantity}
              onPress={val => onChangeQuality(val)}
              onRemoveCart={onRemoveItemCart}
            />
          </View>
        </View>
      </View>
      {/* {cart.combo &&
        cart.combo.length > 0 &&
        cart.combo.map((item, index) => (
          <View
            style={[styles.container, styles.giftContainer]}
            key={`${cart.name}_gift_${index}`}>
            <FastImage
              source={{uri: item.images, priority: 'normal'}}
              style={styles.imageProduct}
            />
            <View style={styles.infoContainer}>
              <Text style={styles.title}>{item.name_vi}</Text>
              <Text style={styles.priceGift}>
                {stringHelper.formatMoney(item.price)} đ
              </Text>
              <View
                style={{
                  flexDirection: 'row',
                  alignItems: 'center',
                  marginTop: 8,
                }}>
                <Text style={styles.priceQuantity}>
                  Số lượng: {stringHelper.formatMoney(item.quantity)}
                </Text>
              </View>
            </View>
          </View>
        ))} */}
      {cart.gift && cart.gift.length > 0 ? (
        <FlatList
          data={cart.gift}
          key="gift_product_checkout"
          keyExtractor={item => `gift_product_checkout_${item.sku}`}
          renderItem={({item}) => (
            <View style={[styles.container, styles.giftContainer]}>
              <FastImage
                source={{uri: item.image_url, priority: 'normal'}}
                style={styles.imageProduct}
              />
              <View style={styles.infoContainer}>
                <Text style={styles.title}>{item.product_name}</Text>
                <Text style={styles.priceGift}>
                {stringHelper.formatMoney(item.price)} đ
                </Text>
                <View
                  style={{
                    flexDirection: 'row',
                    alignItems: 'center',
                    marginTop: 8,
                  }}>
                  <Text style={styles.gifText}>Quà tặng</Text>
                  <Text style={styles.priceQuantity}>
                    Số lượng:{' '}
                    {stringHelper.formatMoney(item.so_luong_su_dung)}
                  </Text>
                </View>
              </View>
            </View>
          )}
        />
      ) : null}
    </View>
  );
};

CartItemComponent.propTypes = {
  cart: PropTypes.object.isRequired,
};

CartItemComponent.defaultProps = {};
export const CartItem = React.memo(
  CartItemComponent,
  (prev, next) => prev.onRemoveItem === next.onRemoveItem,
);

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    padding: 10,
  },
  giftContainer: {
    paddingLeft: 20,
  },
  imageProduct: {
    width: 100,
    height: 100,
    resizeMode: 'contain',
    borderRadius: 4,
  },
  gifText: {
    backgroundColor: '#11d017',
    width: 57,
    height: 18,
    borderRadius: 4,
    justifyContent: 'center',
    alignItems: 'center',
    textAlign: 'center',
    fontSize: 11,
    color: 'white',
  },
  infoContainer: {
    marginLeft: 15,
    flex: 1,
  },
  title: {
    color: '#2a2a2a',
    fontSize: 14,
    lineHeight: 20,
  },
  price: {
    ...globalStyles.text,
    color: '#dc0000',
    lineHeight: 18,
    fontSize: 14,
  },
  priceBefore: {
    ...globalStyles.text,
    textDecorationLine: 'line-through',
    color: '#ccc',
    lineHeight: 18,
    fontSize: 14,
  },
  priceDiscount: {
    color: '#888',
    lineHeight: 18,
    fontSize: 13,
    textDecorationLine: 'line-through',
    paddingLeft: 10,
  },
  priceGift: {
    color: '#2a2a2a',
    fontSize: 14,
    marginTop: 8,
  },
  priceQuantity: {
    color: '#000000',
    fontSize: 13,
    // marginLeft: 10,
  },
  cartAction: {
    marginTop: 8,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
});
