import {colors, globalStyles} from '@app/assets';
import {GLOBAL_FUNC, ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import {stringHelper} from '@app/utils';
import React, {useCallback} from 'react';
import {
  StyleSheet,
  View,
  Image,
  TouchableOpacity,
  ViewPropTypes,
  TextStyle,
} from 'react-native';
import {Text, Rating} from 'react-native-elements';
import FastImage from 'react-native-fast-image';
import PropTypes from 'prop-types';
import {ImageReponsive} from './imageReponsive';
const ProductItemComponent = ({
  product,
  containerStyle,
  hideRating,
  hideTitle,
  imageStyle,
  priceStyle,
  priceDiscountStyle,
  extraParams,
}) => {
  if (!product) {
    return <View />;
  }

  // set icons position
  const iconPostion = () => {
    let icons2h,
      iconQuatang,
      iconGiamgia = null;
    if (product.position_2h_icon_promotion) {
      icons2h = iconsView(
        product.position_2h_icon_promotion,
        product.icon_promotion.icon_giaonhanh2h_json,
      );
    }
    if (product.position_giamgia_icon_promotion) {
      iconGiamgia = iconsView(
        product.position_giamgia_icon_promotion,
        product.icon_promotion.icon_giamgia_json,
        'giamgia',
      );
    }
    if (product.position_gift_icon_promotion) {
      iconQuatang = iconsView(
        product.position_gift_icon_promotion,
        product.icon_promotion.icon_quatang_json,
      );
    }
    return (
      <>
        {icons2h}
        {iconQuatang}
        {iconGiamgia}
      </>
    );
  };
  const iframePromotion = () => {
    if (
      product.promotion_info &&
      product.promotion_info.image_frame &&
      product.promotion_info.is_check_frame == 1
    ) {
      return (
        <Image
          resizeMethod="resize"
          resizeMode="contain"
          style={[styles.position_absolute, styles.fullWidth]}
          source={{
            uri: product.promotion_info.image_frame,
          }}
        />
      );
    }
  };

  const iconsView = (styleicon, objIcon, checkPercent = '') => {
    return (
      <View style={[styles.flexCenterIcons, styleicon]}>
        {objIcon.icon_image_url ? (
          <Image
            resizeMethod="resize"
            resizeMode="contain"
            style={styles.fullWidth}
            source={{
              uri: objIcon.icon_image_url,
            }}
          />
        ) : null}
        {checkPercent ? (
          <Text
            style={{
              position: 'absolute',
              fontSize: 11,
              color: '#fff',
            }}>
            {product.percent}
          </Text>
        ) : null}
      </View>
    );
  };

  // const

  const _renderPrice = () => {
    product = GLOBAL_FUNC.filterPrice(product);
    if (product.price_goc) {
      return (
        <>
          <Text style={[styles.price, priceStyle]}>
            {`${stringHelper.formatMoney(product.price)} đ`}
          </Text>
          <Text style={[styles.priceBefore, priceDiscountStyle]}>
            {`${stringHelper.formatMoney(product.price_goc)} đ`}
          </Text>
        </>
      );
    } else {
      return (
        <>
          <Text style={[styles.price, priceStyle]}>
            {`${stringHelper.formatMoney(product.price)} đ`}
          </Text>
        </>
      );
    }
  };
  const handleOnPressItem = useCallback(() => {
    navigateRoute(
      ROUTES.DETAIL_PRODUCT,
      {...product, ...extraParams},
      `product_detail_${product?.id}`,
    );
  }, []);

  return (
    <TouchableOpacity onPress={handleOnPressItem}>
      <View style={[styles.container, containerStyle]}>
        <View style={{position: 'relative', padding: 0, margin: 0}}>
          <ImageReponsive
            source={{uri: product.images}}
            style={[styles.image, imageStyle]}
            resizeMode="stretch"
          />
          {iframePromotion()}
          {iconPostion()}
        </View>
        <View style={styles.body}>
          {!hideTitle ? (
            <Text style={styles.title} numberOfLines={3}>
              {product.name_vi}
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
              <Text style={styles.viewed_text} numberOfLines={2}>
                {product.random_sao} đã bán
              </Text>
            </View>
          ) : null}

          {_renderPrice()}
        </View>
      </View>
    </TouchableOpacity>
  );
};

ProductItemComponent.propTypes = {
  product: PropTypes.object,
  containerStyle: ViewPropTypes.style,
  imageStyle: ViewPropTypes.style,
  priceStyle: PropTypes.shape(TextStyle),
  priceDiscountStyle: PropTypes.shape(TextStyle),
  hideTitle: PropTypes.bool,
  hideRating: PropTypes.bool,
};

ProductItemComponent.defaultProps = {};
export const ProductItem = React.memo(ProductItemComponent, () => true);

const styles = StyleSheet.create({
  container: {width: 135, position: 'relative', backgroundColor: '#fff'},
  image: {width: '100%', marginBottom: 5},
  rating_box: {flexDirection: 'row', alignItems: 'center'},
  flexCenterIcons: {
    position: 'absolute',
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
  },
  position_absolute: {
    position: 'absolute',
  },
  fullWidth: {
    width: '100%',
    height: '100%',
  },
  body: {
    padding: 4,
    flex: 1,
  },
  rating_box_divide: {
    width: 2,
    height: 8,
    backgroundColor: '#949494',
    marginHorizontal: 4,
  },
  viewed_text: {
    flex: 1,
    color: '#0F83FF',
    fontSize: 11,
    lineHeight: 20,
  },
  title: {
    color: '#000000',
    fontSize: 13,
  },
  price: {
    ...globalStyles.text,
    color: '#dc0000',
    fontSize: 15,
    lineHeight: 18,
    fontWeight: '500',
  },
  hasDiscount: {},
  priceBefore: {
    ...globalStyles.text,
    color: '#888',
    fontSize: 12,
    lineHeight: 18,
    textDecorationLine: 'line-through',
  },
  discount_container: {
    backgroundColor: '#dc0000',
    position: 'absolute',
    height: 18,
    paddingHorizontal: 4,
    borderRadius: 2,
    alignItems: 'center',
    elevation: 1,
    marginTop: 4,
    marginLeft: 4,
  },
  discount_percentText: {
    color: colors.white,
    fontSize: 11,
  },
});
