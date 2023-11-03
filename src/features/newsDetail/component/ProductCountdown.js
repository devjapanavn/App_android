import React from 'react';
import {globalStyles, images, spacing} from '@app/assets';
import {CountdownTime, ImageReponsive, ProductItem} from '@app/components';
import {FlatList, StyleSheet, View} from 'react-native';
import {Button, Divider, Text} from 'react-native-elements';
import {stringHelper} from '@app/utils';
import StarRating from 'react-native-star-rating';
import {iOSColors} from 'react-native-typography';

const product = {
  id: 1,
  img: images.news_detail_product1,
  title: 'Kem chống nắng Atorrege AD+ Moist Up UV SPF14/PA++ 30g',
  sale: 4321,
  price: 850000,
  priceBefore: 999000,
  discountPercent: 10,
};
const ProductCountdownComponent = ({showCountDown}) => {
  return (
    <View style={styles.container}>
      <Divider />
      <View style={{flexDirection: 'row', marginVertical: 10}}>
        <ImageReponsive source={product.img} containerStyle={styles.image} />
        <View style={{flex: 1, marginLeft: 10}}>
          <Text style={styles.title}>{product.title}</Text>
          <View style={styles.rightSection}>
            <Text style={styles.saleText}>
              Đã bán {stringHelper.formatMoney(product.sale)}
            </Text>
            <View style={styles.divider} />
            <Text>4.5</Text>
            <View>
              <StarRating
                containerStyle={styles.rowRatingStar}
                starSize={14}
                disabled={true}
                maxStars={5}
                rating={5}
                fullStarColor={iOSColors.yellow}
              />
            </View>
          </View>
          <View style={styles.rightSection}>
            <Button
              buttonStyle={{borderRadius: 4, backgroundColor: '#2367ff'}}
              title={'SPF - 50ml'}
              iconRight
              icon={{
                name: 'chevron-down-outline',
                type: 'ionicon',
                color: '#fff',
                size: 18,
              }}
            />
          </View>
          <View style={styles.rightSection}>
            <Text style={styles.priceSale}>
              {stringHelper.formatMoney(product.price)}
            </Text>
            <Text style={styles.price}>
              {stringHelper.formatMoney(product.priceBefore)}
            </Text>
          </View>
          {showCountDown ? (
            <View style={styles.rightSection}>
              <CountdownTime
                until={86400}
                onFinish={() => alert('finished')}
                onPress={() => alert('hello')}
                size={10}
                timeToShow={['H', 'M', 'S']}
                timeLabels={{m: null, s: null, h: null}}
                showSeparator
                digitStyle={{backgroundColor: '#ffa200'}}
                digitTxtStyle={{color: '#FFF'}}
              />
            </View>
          ) : null}
          <View style={styles.rightSection}>
            <Button
              buttonStyle={{borderRadius: 4, backgroundColor: '#dc0000'}}
              title={'Thêm vào giỏ'}
              iconRight
            />
          </View>
        </View>
      </View>
      <Divider />
    </View>
  );
};
export const ProductCountdown = React.memo(
  ProductCountdownComponent,
  (prev, next) => false,
);
const styles = StyleSheet.create({
  container: {
    marginVertical: 10,
  },
  image: {
    width: 80,
    height: 80,
    resizeMode: 'contain',
  },
  titleContainer: {
    borderLeftColor: '#2367ff',
    borderLeftWidth: 3,
    paddingVertical: 2,
    paddingLeft: 10,
  },
  title: {
    ...globalStyles.text,
    lineHeight: 20,
    fontSize: 14,
  },
  isSub: {
    marginLeft: 8,
  },
  rowRatingStar: {
    marginHorizontal: 4,
  },
  divider: {
    height: 16,
    width: 1,
    backgroundColor: '#e3e3e3',
    marginHorizontal: 4,
  },
  rightSection: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 6,
  },
  saleText: {
    ...globalStyles.text,
    fontSize: 14,
    color: '#2367ff',
  },
  priceSale: {
    ...globalStyles.text,
    color: '#dc0000',
    fontSize: 18,
    lineHeight: 17,
    marginRight: 10,
  },
  price: {
    ...globalStyles.text,
    color: '#888888',
    fontSize: 14,
    lineHeight: 17,
    marginRight: 10,
    textDecorationLine:'line-through'
  },
});
