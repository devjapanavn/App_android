import {Button, Divider, Icon, Text} from 'react-native-elements';
import {FlatList, StyleSheet, View} from 'react-native';
import {ImageReponsive, ProductItem} from '@app/components';
import React, {useEffect, useState} from 'react';
import {colors, images} from '@app/assets';
import {useDispatch, useSelector} from 'react-redux';

import {BuyTogetherProductItem} from './BuyTogetherProductItem';
import {DataTable} from 'react-native-paper';
import {ROUTES} from '@app/constants';
import _ from 'lodash';
import api from '@app/api';
import {getTotalCart} from '@app/store/auth/services';
import {navigateRoute} from '@app/route';
import {stringHelper} from '@app/utils';

const Component = ({
  productImage,
  productPrice,
  productPricePromotion,
  suggestionData,
  addToCart,
  onPressViewMore,
  mainProductId,
}) => {
  const [listProduct, setListProduct] = useState([]);
  const [isLoading, setLoading] = useState(false);
  const dispatch = useDispatch();
  const {user, isLogin} = useSelector(state => ({
    user: state.auth.user,
    isLogin: state.auth.isLogin,
  }));
  useEffect(() => {
    if (suggestionData && suggestionData.list) {
      setListProduct(suggestionData.list);
    }
  }, [suggestionData]);

  async function buyTogether() {
    if (!isLogin) {
      navigateRoute(ROUTES.LOGIN);
      return false;
    }
    let fetchBuyTogether = [];
    if (listProduct && listProduct.length > 0) {
      fetchBuyTogether.push(api.addToCart(mainProductId, 1, user?.id));
      listProduct.forEach(dt => {
        fetchBuyTogether.push(api.addToCart(dt.id, 1, user?.id));
      });
    }
    if (fetchBuyTogether && fetchBuyTogether.length > 0) {
      setLoading(true);
      const res = await new Promise.all(fetchBuyTogether);
      setLoading(false);
      dispatch(getTotalCart(user?.id));
      setTimeout(() => {
        navigateRoute(ROUTES.CART_LIST, null, null, true);
      }, 300);
    }
  }

  const _renderPrice = () => {
    if (
      productPricePromotion &&
      stringHelper.formatToNumber(productPricePromotion) > 0
    ) {
      return (
        <>
          <Text style={[styles.txtHeaderProductPrice, styles.priceBefore]}>
            {`${stringHelper.formatMoney(productPricePromotion)} đ`}
          </Text>
          <Text style={[styles.txtHeaderProductPrice]}>
            {`${stringHelper.formatMoney(productPrice)} đ`}
          </Text>
        </>
      );
    }
    return (
      <Text style={[styles.txtHeaderProductPrice]}>
        {`${stringHelper.formatMoney(productPrice)} đ`}
      </Text>
    );
  };

  const renderHeader = () => {
    return (
      <View style={styles.headerProductContainer}>
        <View>
          <ImageReponsive
            source={{
              uri: productImage,
            }}
            containerStyle={styles.txtHeaderProductImage}
          />
          {_renderPrice()}
        </View>

        <Icon
          type="foundation"
          name="plus"
          style={styles.iconHeaderPlus}
          color="#0F83FF"
          size={30}
        />
      </View>
    );
  };
  const renderProduct = ({item, index}) => {
    return (
      <BuyTogetherProductItem
        product={item}
        imageStyle={styles.itemProductImage}
        containerStyle={styles.itemProduct}
        priceStyle={styles.itemProductPrice}
        priceDiscountStyle={styles.itemProductDiscount}
        hideTitle={true}
        hideRating={true}
      />
    );
  };

  return (
    <View style={styles.box}>
      <View style={styles.headerContainer}>
        <Text style={styles.headerTitleStyle}>Thường được mua cùng</Text>
        <Button
          type="clear"
          iconRight
          title="Chi tiết"
          titleStyle={styles.headerTitleMoreStyle}
          onPress={onPressViewMore}
          icon={{
            type: 'ionicon',
            name: 'chevron-forward',
            size: 12,
            color: '#0f83ff',
          }}
        />
      </View>
      <FlatList
        key={'product_buy_together'}
        keyExtractor={item => 'product_buy_together_' + item.id}
        showsHorizontalScrollIndicator={false}
        ListHeaderComponent={renderHeader}
        style={styles.flatlistContainer}
        horizontal
        ItemSeparatorComponent={() => <View style={styles.itemSeparator} />}
        data={listProduct}
        renderItem={renderProduct}
      />
      <Divider />
      <View style={styles.footerContainer}>
        <View>
          <Text style={styles.footerTitle}>
            Tổng cộng:{' '}
            <Text>
              {stringHelper.formatMoney(suggestionData?.total_purchase)} đ
            </Text>
          </Text>
          <Text style={styles.footerSubTitle}>
            ({suggestionData?.total_quantity} sản phẩm)
          </Text>
        </View>
        <Button
          loading={isLoading}
          disabled={isLoading}
          onPress={buyTogether}
          title={'Mua cùng'}
          buttonStyle={styles.footerButton}
          titleStyle={styles.footerButtonTitle}
        />
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  box: {
    marginVertical: 4,
    padding: 10,
    backgroundColor: '#fff',
  },
  headerContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  headerTitleStyle: {
    fontSize: 16,
    color: '#000',
    fontWeight: '500',
    flex: 1,
  },
  priceBefore: {
    color: '#ccc',
    fontSize: 12,
    lineHeight: 18,
    textDecorationLine: 'line-through',
  },
  headerProductContainer: {flexDirection: 'row', alignItems: 'center'},
  txtHeaderProductImage: {width: 60, height: 60},
  txtHeaderProductPrice: {
    fontSize: 12,
    fontWeight: '500',
    color: colors.black,
  },
  iconHeaderPlus: {
    marginHorizontal: 20,
  },
  headerTitleMoreStyle: {
    fontSize: 12,
  },
  footerContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginVertical: 10,
  },
  footerTitle: {
    color: '#000',
    fontSize: 13,
    lineHeight: 18,
  },
  footerButton: {
    backgroundColor: 'rgb(220, 0, 0)',
    padding: 10,
  },
  footerButtonTitle: {
    fontSize: 16,
    color: '#ffffff',
    fontWeight: 'bold',
  },
  footerSubTitle: {
    color: '#555',
    fontSize: 12,
    lineHeight: 18,
  },
  itemProduct: {
    width: null,
    margin: 5,
  },
  itemProductImage: {
    width: 60,
    height: 60,
  },
  itemProductPrice: {
    fontSize: 11,
  },
  itemProductDiscount: {
    color: '#888',
    fontSize: 11,
  },
  itemSeparator: {
    width: 18,
  },
});

function areEqual(prev, next) {
  return (
    _.isEqual(prev.suggestionData, next.suggestionData) &&
    prev.onPressViewMore === next.onPressViewMore
  );
}
export const ProductSuggest = React.memo(Component, areEqual);
