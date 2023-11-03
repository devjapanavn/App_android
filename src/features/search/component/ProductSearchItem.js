import {Rating, Text} from 'react-native-elements';
import React, {useCallback} from 'react';
import {
  StyleSheet,
  TextStyle,
  TouchableOpacity,
  View,
  ViewPropTypes,
} from 'react-native';

import FastImage from 'react-native-fast-image';
import PropTypes from 'prop-types';
import {ROUTES} from '@app/constants';
import {colors} from '@app/assets';
import {navigateRoute} from '@app/route';
import {stringHelper} from '@app/utils';

const ProductSearchItemComponent = ({
  product,
  containerStyle,
  hideTitle,
  imageStyle,
  priceStyle,
  extraParams,
}) => {
  const handleOnPressItem = useCallback(() => {
    navigateRoute(
      ROUTES.DETAIL_PRODUCT,
      {...product, ...extraParams},
      `product_detail_${product?.id}`,
    );
  }, []);
  console.log('-----------Search-----------');
  console.log(product);
  const _renderPrice = () => {
    if (
      product.price_promotion &&
      stringHelper.formatToNumber(product.price_promotion) > 0
    ) {
      return (
        <View style={{flexDirection: 'row'}}>
          <Text style={[styles.price, priceStyle]}>
            {`${stringHelper.formatMoney(product.price)} đ`}
          </Text>
          <Text style={[styles.priceBefore]}>
            {`${stringHelper.formatMoney(product.price_promotion)} đ`}
          </Text>
        </View>
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

  const _render_icon_gift = () => {
    if(product?.position_gift_icon_promotion){
      return <FastImage
        source={{uri: product.position_gift_icon_promotion.icon_image_url}}
        style={styles.icon_image}
        resizeMode="contain"
      />;
    }
  };
  return (
    <TouchableOpacity onPress={handleOnPressItem}>
      <View style={[styles.container, containerStyle]}>
        <View style={styles.box_image}>
          <FastImage
            source={{uri: product.images}}
            style={[styles.image, imageStyle]}
            resizeMode="contain"
          />
          {_render_icon_gift()}
        </View>
        <View style={{flex: 1}}>
          {!hideTitle ? (
            <Text style={styles.title} numberOfLines={3}>
              {product.name_vi}
            </Text>
          ) : null}
          {_renderPrice()}
        </View>
      </View>
    </TouchableOpacity>
  );
};

ProductSearchItemComponent.propTypes = {
  product: PropTypes.object.isRequired,
  containerStyle: ViewPropTypes.style,
  imageStyle: ViewPropTypes.style,
  priceStyle: PropTypes.shape(TextStyle),
  priceDiscountStyle: PropTypes.shape(TextStyle),
  hideTitle: PropTypes.bool,
};

ProductSearchItemComponent.defaultProps = {};
export const ProductSearchItem = React.memo(
  ProductSearchItemComponent,
  () => true,
);

const styles = StyleSheet.create({
  container: {
    position: 'relative',
    flexDirection: 'row',
    alignItems: 'center',
    height: 100,
  },
  icon_image: {
    position: 'absolute', width: 20, height: 20, left: 1, bottom: 1
  },
  box_image: {
    position: 'relative', margin: 10
  },
  image: {
    width: 80,
    height: 80,
    // margin: 10,
    borderRadius: 4,
    borderWidth: 1,
    borderColor: '#d9d9d9',
  },
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
    marginBottom: 5,
  },
  price: {
    color: '#dc0000',
    fontSize: 15,
    lineHeight: 18,
    fontWeight: '500',
    paddingRight: 10,
  },
  hasDiscount: {},
  priceBefore: {
    color: '#888',
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
