import React, {useEffect, useState} from 'react';
import {StyleSheet, TouchableOpacity, View, FlatList, ScrollView, Dimensions} from 'react-native';
import {Text} from 'react-native-elements';
import _ from 'lodash';
import {ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import FastImage from 'react-native-fast-image';
import {orderSaleAuto} from '@app/store/checkout/services';
import {useDispatch} from 'react-redux';
const windowDimensions = Dimensions.get('window');

const component = ({data, listVoucher}) => {
  const [giftData, setGiftData] = useState([]);
  const dispatch = useDispatch();

  useEffect(() => {
    setGiftData(data);
    dispatch(
      orderSaleAuto({
        gift_order_auto: JSON.stringify(data),
        voucher_code: listVoucher,
      }),
    );
  }, [data, listVoucher]);
  const showVoucherDetails = () => {
    navigateRoute(ROUTES.SALE_AUTO, {onSelect, data}, false, true);
  };
  function onSelect(location) {
    setGiftData(location);

    dispatch(
      orderSaleAuto({
        gift_order_auto: JSON.stringify(location),
        voucher_code: listVoucher,
      }),
    );
  }
  return (
    <>
        <ScrollView horizontal={true} style={{width: '100%'}}>
          <FlatList
            data={giftData}
            key="product_sale_auto"
            // horizontal={true}
            style={{width: windowDimensions.width - 20}}
            keyExtractor={item => `product_sale_auto_${item.sku}`}
            renderItem={({item}) => {
              if (item.check_active == 1) {
                return (
                  <View style={styles.row}>
                    <FastImage
                      source={{uri: item.image_url}}
                      style={styles.productImage}
                    />
                    <View style={styles.productInfo}>
                      <Text style={styles.productInfoTitle}>
                        {item.product_name}
                      </Text>
                      <Text style={styles.gifText}>Quà tặng</Text>
                      <Text style={styles.productInfoPrice}>
                        Số lượng: {item.so_luong}
                      </Text>
                      <TouchableOpacity onPress={showVoucherDetails}>
                        <Text style={styles.btnChangeGift}>Đổi quà tặng</Text>
                      </TouchableOpacity>
                    </View>
                  </View>
                );
              }
            }}
          />
        </ScrollView>

    </>
  );
};

export const ProductGiftSaleAuto = React.memo(component);
const styles = StyleSheet.create({
  btnChangeGift: {
    color: '#2367FF',
  },
  borderDash: {
    borderBottomColor: '#888',
    borderBottomWidth: 1,
    borderStyle: 'dashed',
    marginBottom: 10,
  },
  row: {
    paddingHorizontal: 0,
    marginVertical: 5,
    flexDirection: 'row',
  },
  text: {
    fontSize: 13,
    color: '#000',
    lineHeight: 22,
  },
  footerTable: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    padding: 5,
  },
  footerText: {
    fontSize: 14,
    color: '#000',
  },
  productImage: {
    width: 60,
    height: 60,
    resizeMode: 'contain',
    marginHorizontal: 5,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#d9d9d9',
  },
  productInfo: {
    flex: 1,
    marginHorizontal: 5,
  },
  productInfoTitle: {
    fontSize: 13,
    lineHeight: 20,
    fontFamily: 'SF Pro Display',
  },
  productInfoSubTitle: {
    fontSize: 13,
    lineHeight: 20,
    color: '#3b4859',
    fontFamily: 'SF Pro Display',
  },
  productInfoPrice: {
    fontSize: 13,
    lineHeight: 20,
    color: '#000',
    fontWeight: '500',
    fontFamily: 'SF Pro Display',
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
});
