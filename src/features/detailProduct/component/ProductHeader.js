import {colors, globalStyles, images, spacing} from '@app/assets';
import React, { useEffect } from 'react';
import {StyleSheet, View, ImageBackground} from 'react-native';
import {Text} from 'react-native-elements';
import StarRating from 'react-native-star-rating';
import {iOSColors} from 'react-native-typography';
import {stringHelper} from '@app/utils';
import _ from 'lodash';
import {DiscountTime} from './DiscountTime';
import { GLOBAL_FUNC } from 'src/constants';

const Component = ({detail, selectedProduct}) => {

  const renderPrice = () => {
    detail = GLOBAL_FUNC.filterPrice(detail)
    if(selectedProduct){
      selectedProduct = GLOBAL_FUNC.filterPrice(selectedProduct)
      if(selectedProduct.price_goc){
        detail.price_goc = selectedProduct.price_goc;
      }
      if(selectedProduct.price){
        detail.price = selectedProduct.price;
      }
    }

    if(detail.price_goc){
      return (
        <>
          <Text style={[styles.price,styles.price_sale]}>
             {stringHelper.formatMoney(detail.price)} đ
          </Text>
          <Text style={[styles.price_discount, styles.price]}>
             {stringHelper.formatMoney(detail.price_goc)} đ
          </Text>
        </>
      )
    }else{
      return (
        <Text style={[styles.price,styles.price_sale]}>
           {stringHelper.formatMoney(detail.price)} đ
        </Text>
      )
    }
  };
  return (
    <>
      <View style={styles.section}>
        <View style={{flexDirection: 'row'}}>
          <Text style={styles.productTitle}>{detail.name_vi}</Text>
          {detail.percent  ? (
            <ImageBackground
              source={images.discount_tag}
              resizeMode="contain"
              style={styles.discountTag}>
              <Text
                style={[
                  styles.discountTagText,
                  {
                    color: colors.primary,
                  },
                ]}>
                {detail.percent}
              </Text>
              <Text style={[styles.discountTagText, {color: '#fff'}]}>
                Giảm
              </Text>
            </ImageBackground>
          ) : null}
        </View>
        <View style={styles.box}>
          <StarRating
            containerStyle={{marginRight: spacing.small}}
            starSize={20}
            disabled={true}
            maxStars={5}
            rating={stringHelper.formatToNumber(detail.rating)}
            fullStarColor={iOSColors.yellow}
            emptyStarColor={iOSColors.gray}
          />
          <Text style={styles.rating_text}>
            {detail.data_rating?.medium_rate || detail.rating}
          </Text>
          <View style={styles.rating_divide} />
          <Text style={styles.sale_number}>
            Đã bán {stringHelper.formatMoney(detail.random_sao)}
          </Text>
        </View>
        <View style={styles.box}>{renderPrice()}</View>
        <DiscountTime enDate={selectedProduct.date_end} title={selectedProduct.mota} />
      </View>
    </>
  );
};

function areEqual(prev, next) {
  return (
    _.isEqual(prev.detail, next.detail) &&
    _.isEqual(prev.selectedProduct, next.selectedProduct)
  );
}
export const ProductHeader = React.memo(Component, areEqual);
const styles = StyleSheet.create({
  section: {
    padding: 10,
    backgroundColor: '#fff',
    elevation: 1,
  },
  productTitle: {
    flex: 1,
    color: '#000000',
    fontSize: 17,
    lineHeight: 26,
    marginBottom: 5,
  },
  discountTag: {
    alignItems: 'center',
    paddingTop: 2,
    width: 46,
    height: 48,
  },
  discountTagText: {
    ...globalStyles.text,
    fontSize: 11,
    lineHeight: 13,
  },
  box: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 4,
  },
  rating_text: {
    fontSize: 14,
    color: '#2a2a2a',
  },
  rating_divide: {
    width: 1,
    height: 15,
    backgroundColor: '#e3e3e3',
    marginHorizontal: 5,
  },
  sale_number: {
    color: '#000000',
    fontSize: 14,
  },
  price: {
    marginRight: 8,
  },
  price_sale: {
    color: '#dc0000',
    fontSize: 18,
  },
  price_discount: {
    color: '#888888',
    fontSize: 14,
    textDecorationLine: 'line-through',
  },
});
