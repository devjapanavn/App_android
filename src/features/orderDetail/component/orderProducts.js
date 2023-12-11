import {colors} from '@app/assets';
import {ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import {stringHelper} from '@app/utils';
import _ from 'lodash';
import React from 'react';
import {TouchableOpacity, View, FlatList, Dimensions} from 'react-native';
import {Chip, Divider, Icon, Text} from 'react-native-elements';
import FastImage from 'react-native-fast-image';
import styles from '../styles';
import { ScrollView } from 'react-native';
const windowDimensions = Dimensions.get('window');

const component = ({products, payments, gift,giftOrderAuto}) => {
  // console.log(giftOrderAuto)
  const footer = () => {
    if (payments) {
      const arr = Object.values(payments);
      // console.log(payments)
      // console.log(payments.list_coupon)
      // arr.splice(toIndex, 0, element);
      if (arr && arr.length > 0) {
        return (
          <View style={styles.footer}>
            <Divider />
            {arr.map((item, index) => {
              if (_.isArray(item)) {
                return (
                  <View>
                    {item?.map((child, childIndex) => {
                      return (
                        <View
                          style={styles.footerRow}
                          key={`payment_${index}_${childIndex}`}>
                          <Text style={styles.footerLeftTitle}>
                            {child.text}
                          </Text>
                          <Text
                            style={
                              index === arr.length - 1
                                ? {
                                    color: colors.link,
                                    fontWeight: 'bold',
                                    fontSize: 15,
                                  }
                                : null
                            }>
                            {stringHelper.formatMoney(child.value)} đ
                          </Text>
                        </View>
                      );
                    })}
                  </View>
                );
              } else if (_.isObject(item)) {
                if (item.text == 'Mã KM' && item.value.length > 0) {
                  return (
                    <View style={{flexDirection: 'row',marginTop: 10}}>
                      {item.value.map(cp => (
                        <Chip
                          key={cp}
                          buttonStyle={{
                            padding: 4,
                            marginRight: 4,
                          }}
                          title={cp}
                          type="solid"
                        />
                      ))}
                    </View>
                  );
                }else{
                  return (
                    <View style={styles.footerRow} key={`payment_${index}`}>
                      <Text style={styles.footerLeftTitle}>{item.text}</Text>
                      <Text
                        style={
                          index === arr.length - 1
                            ? {
                                color: colors.link,
                                fontWeight: 'bold',
                                fontSize: 15,
                              }
                            : null
                        }>
                        {stringHelper.formatMoney(item.value)} đ
                      </Text>
                    </View>
                  );
                }
              }
            })}
          </View>
        );
      }
    }
    return <View />;
  };
  const giftProduct = () => {
    if (gift) {
      return (
        <ScrollView horizontal={true} style={{width: '100%'}}>
          <FlatList
          data={gift}
          style={{width: windowDimensions.width - 30}}
          horizontal={false}
          key={'order_product_gift'}
          keyExtractor={itemGift => 'order_product_gift_' + itemGift.id_product}
          renderItem={itemGift => {
            return (
              <TouchableOpacity
                style={styles.row}
                onPress={() =>
                  navigateRoute(
                    ROUTES.DETAIL_PRODUCT,
                    {id: itemGift.item.id_product},
                    `product_detail_${itemGift.item.id_product}`,
                  )
                }>
                <FastImage
                  source={{uri: itemGift.item.image_url}}
                  style={styles.productImage}
                />
                <View style={styles.productInfo}>
                  <Text style={styles.productInfoTitle}>
                    {itemGift.item.product_name}
                  </Text>
                  <Text style={styles.gifText}>Quà tặng</Text>
                </View>
              </TouchableOpacity>
            );
          }}
        />
        </ScrollView>
      );
    }
  };
  return (
    <>
      <View style={styles.box}>
        <Text style={styles.boxTitle}>Sản phẩm </Text>
        {products &&
          products.length > 0 &&
          products.map(item => (
            <>
              <TouchableOpacity
                style={styles.row}
                onPress={() =>
                  navigateRoute(
                    ROUTES.DETAIL_PRODUCT,
                    {id: item.id_product},
                    `product_detail_${item.id}`,
                  )
                }
                key={'order_product_' + item.id}>
                <FastImage
                  source={{uri: item.images}}
                  style={styles.productImage}
                />
                <View style={styles.productInfo}>
                  <Text style={styles.productInfoTitle}>{item.name}</Text>
                  <Text style={styles.productInfoPrice}>
                    {stringHelper.formatMoney(item.total)} đ
                  </Text>
                  <Text style={styles.productInfoPrice}>
                    Số lượng: {item.qty}
                  </Text>
                </View>
              </TouchableOpacity>
            </>
          ))}
        {giftProduct()}
        {footer()}
      </View>
    </>
  );
};
export const OrderProducts = React.memo(
  component,
  (prev, next) =>
    _.isEqual(prev.products, next.products) &&
    _.isEqual(prev.payments, next.payments),
);
