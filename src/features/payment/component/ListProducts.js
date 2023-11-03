import React from 'react';
import {StyleSheet, TouchableOpacity, FlatList, View, ScrollView, Dimensions} from 'react-native';
import {Chip, Text} from 'react-native-elements';
import {DataTable} from 'react-native-paper';
import {stringHelper} from '@app/utils';
import _ from 'lodash';
import {ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import FastImage from 'react-native-fast-image';
import {iOSColors} from 'react-native-typography';
import {ProductCheckout} from './productCheckout';
import { ProductGiftSaleAuto } from './productGiftSaleAuto';
const windowDimensions = Dimensions.get('window');

// const ProductPayment = React.memo(
//   ({item}) => {
//     let combos = [];
//     if (item.combo) {
//       if (_.isString(item.combo)) {
//         try {
//           combos = JSON.parse(item.combo);
//         } catch (error) {
//           console.log('error', error);
//           combos = [];
//         }
//       } else if (_.isArray(item.combo)) {
//         combos = item.combo;
//       }
//     }
//     console.log('item.combo', typeof item.combo);
//     console.log('combos', combos);
//     return (
//       <>
//         <TouchableOpacity
//           style={styles.row}
//           onPress={() =>
//             navigateRoute(
//               ROUTES.DETAIL_PRODUCT,
//               {id: item.id_product},
//               `product_detail_${item.id}`,
//             )
//           }
//           key={'payment_product_' + item.id}>
//           <FastImage source={{uri: item.images}} style={styles.productImage} />
//           <View style={styles.productInfo}>
//             <Text style={styles.productInfoTitle}>{item.name}</Text>
//             <Text style={styles.productInfoPrice}>
//               {stringHelper.formatMoney(item.total)}  đ
//             </Text>
//             <Text style={styles.productInfoPrice}>Số lượng: {item.qty}</Text>
//           </View>
//         </TouchableOpacity>
//         {combos
//           ? combos.map(combo => {
//               return (
//                 <TouchableOpacity
//                   style={styles.row}
//                   onPress={() =>
//                     navigateRoute(
//                       ROUTES.DETAIL_PRODUCT,
//                       {id: combo.id},
//                       `product_detail_combo${combo.id}`,
//                     )
//                   }
//                   key={'order_product_combo_' + item.id + '_' + combo.id}>
//                   <FastImage
//                     source={{uri: combo.images}}
//                     style={styles.productImage}
//                   />
//                   <View style={styles.productInfo}>
//                     <Text style={styles.productInfoTitle}>{combo.name_vi}</Text>
//                     <Text
//                       style={[
//                         styles.gifText,
//                         {backgroundColor: iOSColors.orange},
//                       ]}>
//                       Combo
//                     </Text>
//                     <Text style={styles.productInfoPrice}>
//                       {stringHelper.formatMoney(combo.price)} đ
//                     </Text>
//                     <Text style={styles.productInfoPrice}>
//                       Số lượng: {combo.quantity}
//                     </Text>
//                   </View>
//                 </TouchableOpacity>
//               );
//             })
//           : null}
//         {item && item.product_gift && item.product_gift.id ? (
//           <TouchableOpacity
//             style={styles.row}
//             onPress={() =>
//               navigateRoute(
//                 ROUTES.DETAIL_PRODUCT,
//                 {id: item.product_gift.id},
//                 `product_detail_${item.product_gift.id}`,
//               )
//             }
//             key={'order_product_gift_' + item.product_gift.id}>
//             <FastImage
//               source={{uri: item.product_gift.images}}
//               style={styles.productImage}
//             />
//             <View style={styles.productInfo}>
//               <Text style={styles.productInfoTitle}>
//                 {item.product_gift.name_vi}
//               </Text>
//               <Text style={styles.gifText}>Quà tặng</Text>
//               <Text style={styles.productInfoPrice}>
//                 Số lượng: {item.product_gift.quantity}
//               </Text>
//             </View>
//           </TouchableOpacity>
//         ) : null}
//       </>
//     );
//   },
//   (prev, next) => true,
// );
const Footer = ({payments}) => {
  if (!payments) {
    return null;
  }
  const arr = Object.values(payments);
  if (arr && arr.length > 0) {
    return (
      <>
        {arr.map(item => (
          <>
            {item.text == 'Mã KM' && item.value.length > 0 ? (
              null
              // <View style={{flexDirection: 'row'}}>
              //   {item.value.map(cp => (
              //     <Chip
              //       key={cp}
              //       buttonStyle={{
              //         padding: 4,
              //         marginRight: 4,
              //       }}
              //       title={cp}
              //       type="solid"
              //     />
              //   ))}
              // </View>
            ) : (
              <View style={styles.footerTable} key={'price_total_'+ item.text}>
                <Text>{item.text}</Text>
                <Text> {stringHelper.formatMoney(item.value)}đ</Text>
              </View>
            )}
          </>
        ))}
      </>
    );
  }
  return <View />;
};
export const PaymentProducts = React.memo(
  ({products, payments, saleAuto}) => {
    return (
      <>
        <ScrollView horizontal={true} style={{width: '100%'}}>
          <FlatList
            style={{width: windowDimensions.width - 20}}
            data={products}
            key="product_checkout"
            keyExtractor={item => `product_checkout_${item.sku}`}
            renderItem={({item}) => <ProductCheckout item={item} />}
          />
        </ScrollView>
        {saleAuto && saleAuto.length > 0 ? (
          <ProductGiftSaleAuto
            data={saleAuto}
            listVoucher={payments?.list_coupon}
          />
        ) : null}
        {payments && payments.gift ? (
        <ScrollView horizontal={true} style={{width: '100%'}}>
          <FlatList
            style={{width: windowDimensions.width - 20}}
            data={payments.gift}
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
                  <Text style={styles.productInfoTitle}>
                    {item.product_name}
                  </Text>
                  <Text style={styles.gifText}>Quà tặng</Text>
                  <Text style={styles.productInfoPrice}>
                    Số lượng: {item.so_luong_su_dung}
                  </Text>
                </View>
              </TouchableOpacity>
            )}
          />
        </ScrollView>
        ) : null}
        <View style={styles.borderDash} />
        <Footer payments={payments?.payment} />
      </>
    );
  },
  (prev, next) =>
    _.isEqual(prev.products, next.products) &&
    _.isEqual(prev.payments, next.payments),
);

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
