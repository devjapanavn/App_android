import React, {useState} from 'react';
import {StyleSheet, TouchableOpacity, View, FlatList, Dimensions} from 'react-native';
import {Chip, Text} from 'react-native-elements';
import {stringHelper} from '@app/utils';
import _ from 'lodash';
import {ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import FastImage from 'react-native-fast-image';
import {iOSColors} from 'react-native-typography';

const component = ({item}) => {

  let [combos, setCombos] = useState();
  useState(() => {
    if (item.combo) {
      if (_.isString(item.combo)) {
        try {
          setCombos(JSON.parse(item.combo));
          combos = JSON.parse(item.combo);
        } catch (error) {
          console.log('error', error);
          combos = [];
        }
      } else if (_.isArray(item.combo)) {
        setCombos(item.combo);
        combos = item.combo;
      }
    }
  }, [item]);
  console.log('item', item);
  return (
    <>
      <TouchableOpacity
        style={[styles.row]}
        onPress={() =>
          navigateRoute(
            ROUTES.DETAIL_PRODUCT,
            {id: item.id_product},
            `product_detail_${item.id}`,
          )
        }
        key={'payment_product_' + item.id}>
        <FastImage source={{uri: item.images}} style={styles.productImage} />
        <View style={styles.productInfo}>
          <Text style={styles.productInfoTitle}>{item.name}</Text>
          {item.combo  && item.combo.length > 0 ? (<Text
            style={[
              styles.gifText,
              {backgroundColor: iOSColors.orange},
            ]}>
            Combo
          </Text>) : null}
          <Text style={styles.productInfoPrice}>
            {stringHelper.formatMoney(item.total)} đ
          </Text>
          <Text style={styles.productInfoPrice}>Số lượng: {item.qty}</Text>
        </View>
      </TouchableOpacity>
      {/* {combos
        ? combos.map(combo => {
            return (
              <TouchableOpacity
                style={styles.row}
                onPress={() =>
                  navigateRoute(
                    ROUTES.DETAIL_PRODUCT,
                    {id: combo.id},
                    `product_detail_combo${combo.id}`,
                  )
                }
                key={'order_product_combo_' + item.id + '_' + combo.id}>
                <FastImage
                  source={{uri: combo.images}}
                  style={styles.productImage}
                />
                <View style={styles.productInfo}>
                  <Text style={styles.productInfoTitle}>{combo.name_vi}</Text>
                  <Text
                    style={[
                      styles.gifText,
                      {backgroundColor: iOSColors.orange},
                    ]}>
                    Combo
                  </Text>
                  <Text style={styles.productInfoPrice}>
                    {stringHelper.formatMoney(combo.price)} đ
                  </Text>
                  <Text style={styles.productInfoPrice}>
                    Số lượng: {combo.quantity}
                  </Text>
                </View>
              </TouchableOpacity>
            );
          })
        : null} */}
      {/* {item && item.product_gift && item.product_gift.id ? (
        <TouchableOpacity
          style={styles.row}
          onPress={() =>
            navigateRoute(
              ROUTES.DETAIL_PRODUCT,
              {id: item.product_gift.id},
              `product_detail_${item.product_gift.id}`,
            )
          }
          key={'order_product_gift_' + item.product_gift.id}>
          <FastImage
            source={{uri: item.product_gift.images}}
            style={styles.productImage}
          />
          <View style={styles.productInfo}>
            <Text style={styles.productInfoTitle}>
              {item.product_gift.name_vi}
            </Text>
            <Text style={styles.gifText}>Quà tặng</Text>
            <Text style={styles.productInfoPrice}>
              Số lượng: {item.product_gift.quantity}
            </Text>
          </View>
        </TouchableOpacity>
      ) : null} */}
      {/* {item && item.gift ? (
        <FlatList
          data={item.gift}
          key="gift_product_checkout"
          keyExtractor={item => `gift_product_checkout_${item.sku}`}
          renderItem={({item}) => (
            <TouchableOpacity
              style={styles.row}
              onPress={() =>
                navigateRoute(
                  ROUTES.DETAIL_PRODUCT,
                  {id: item.id_product},
                  `product_detail_${item.id_product}`,
                )
              }
              key={'order_gift_' + item.id_product}>
              <FastImage
                source={{uri: item.image_url}}
                style={styles.productImage}
              />
              <View style={styles.productInfo}>
                <Text style={styles.productInfoTitle}>{item.product_name}</Text>
                <Text style={styles.gifText}>Quà tặng</Text>
                <Text style={styles.productInfoPrice}>
                  Số lượng: {item.so_luong_su_dung}
                </Text>
              </View>
            </TouchableOpacity>
          )}
        />
      ) : null} */}
    </>
  );
};

export const ProductCheckout = React.memo(component);
const styles = StyleSheet.create({
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
