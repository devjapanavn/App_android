import {colors} from '@app/assets';
import {GLOBAL_FUNC, ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import {stringHelper} from '@app/utils';
import React, {useCallback} from 'react';
import {
  StyleSheet,
  View,
  TouchableOpacity,
  ViewPropTypes,
  TextStyle,
} from 'react-native';
import {Text, Rating} from 'react-native-elements';
import FastImage from 'react-native-fast-image';
import PropTypes from 'prop-types';
const ProductItemComponent = ({
  product,
  containerStyle,
  hideRating,
  hideTitle,
  imageStyle,
  priceStyle,
  priceDiscountStyle,
}) => {
  const _renderDiscount = (
    <View style={styles.discount_container}>
      <Text style={styles.discount_percentText}>{product.percent}</Text>
    </View>
  );
  const _renderPrice = () => {
    product = GLOBAL_FUNC.filterPrice(product);
    if(product.price_goc){
      return (
        <>
          <Text style={[styles.priceBefore, priceStyle]}>
            {`${stringHelper.formatMoney(product.price_goc)} đ`}
          </Text>
          <Text style={[styles.price, priceStyle]}>
            {`${stringHelper.formatMoney(product.price)} đ`}
          </Text>
        </>
      );
    }else{
      return (
        <Text style={[styles.price, priceStyle]}>
          {`${stringHelper.formatMoney(product.price)} đ`}
        </Text>
      );
    }
  };
  const handleOnPressItem = useCallback(() => {
    navigateRoute(
      ROUTES.DETAIL_PRODUCT,
      product,
      `product_detail_${product?.id}`,
    );
  }, []);

  return (
    <TouchableOpacity onPress={handleOnPressItem}>
      <View style={[styles.container, containerStyle]}>
        <FastImage
          source={{uri: product.image}}
          style={[styles.image, imageStyle]}
          resizeMode="contain"
        />
        {!hideTitle ? (
          <Text style={styles.title} numberOfLines={2}>
            {product.name}
          </Text>
        ) : null}
        {!hideRating ? (
          <View style={styles.rating_box}>
            <Rating
              ratingCount={5}
              startingValue={stringHelper.formatToNumber(product.rating)}
              ratingColor={'#FFCC01'}
              imageSize={12}
              readonly
            />
            <View style={styles.rating_box_divide} />
            <Text style={styles.viewed_text}>{product.id_user_showview}</Text>
          </View>
        ) : null}
        {_renderPrice()}
        {product.percent ? _renderDiscount : null}
      </View>
    </TouchableOpacity>
  );
};

ProductItemComponent.propTypes = {
  product: PropTypes.object.isRequired,
  containerStyle: ViewPropTypes.style,
  imageStyle: ViewPropTypes.style,
  priceStyle: PropTypes.shape(TextStyle),
  priceDiscountStyle: PropTypes.shape(TextStyle),
  hideTitle: PropTypes.bool,
  hideRating: PropTypes.bool,
};

ProductItemComponent.defaultProps = {};
export const BuyTogetherProductItem = React.memo(
  ProductItemComponent,
  () => true,
);

const styles = StyleSheet.create({
  container: {width: 130, position: 'relative'},
  image: {width: '100%', height: 130, marginBottom: 5},
  rating_box: {flexDirection: 'row', alignItems: 'center'},
  rating_box_divide: {
    width: 2,
    height: 8,
    backgroundColor: '#949494',
    marginHorizontal: 4,
  },
  viewed_text: {
    color: '#0F83FF',
    fontSize: 12,
    lineHeight: 20,
  },
  title: {
    color: '#000000',
    fontSize: 13,
  },
  price: {
    color: '#000',
    fontSize: 15,
    lineHeight: 18,
    fontWeight: '500',
  },
  hasDiscount: {},
  priceBefore: {
    color: '#ccc',
    fontSize: 12,
    lineHeight: 18,
    textDecorationLine: 'line-through',
  },
  discount_container: {
    backgroundColor: '#dc0000',
    position: 'absolute',
    width: 35,
    height: 18,
    borderRadius: 2,
    alignItems: 'center',
    elevation: 1,
  },
  discount_percentText: {
    color: colors.white,
    fontSize: 12,
  },
});
