import React from 'react';
import {globalStyles, images} from '@app/assets';
import {ProductItem} from '@app/components';
import {FlatList, StyleSheet, View} from 'react-native';
import {Text} from 'react-native-elements';

const products = [
  {
    id: 1,
    img: images.product_1,
    title: 'Viên uống trị nám trắng da Vitamin C Takeda 2000mg',
    viewed: 345,
    price: 850000,
    priceBefore: 999000,
    discountPercent: 10,
  },
  {
    id: 2,
    img: images.product_2,
    title: 'Viên uống trị nám trắng da Vitamin C Takeda 2000mg',
    viewed: 345,
    price: 850000,
    priceBefore: 999000,
    discountPercent: 0,
  },
  {
    id: 3,
    img: images.product_3,
    title: 'Viên uống trị nám trắng da Vitamin C Takeda 2000mg',
    viewed: 345,
    price: 850000,
    priceBefore: 999000,
    discountPercent: 30,
  },
  {
    id: 6,
    img: images.product_1,
    title: 'Viên uống trị nám trắng da Vitamin C Takeda 2000mg',
    viewed: 345,
    price: 850000,
    priceBefore: 999000,
    discountPercent: 10,
  },
  {
    id: 4,
    img: images.product_2,
    title: 'Viên uống trị nám trắng da Vitamin C Takeda 2000mg',
    viewed: 345,
    price: 850000,
    priceBefore: 999000,
    discountPercent: 0,
  },
  {
    id: 5,
    img: images.product_3,
    title: 'Viên uống trị nám trắng da Vitamin C Takeda 2000mg',
    viewed: 345,
    price: 850000,
    priceBefore: 999000,
    discountPercent: 30,
  },
];
const ProductHorizonComponent = () => {
  const _renderItem = ({item, index}) => {
    return <ProductItem product={item} />;
  };

  return (
    <View>
      <View style={styles.titleContainer}>
        <Text style={styles.title}>Top 4 kem chống nắng tốt nhất hiện nay</Text>
      </View>
      <FlatList
        contentContainerStyle={styles.container}
        horizontal
        showsHorizontalScrollIndicator={false}
        data={products}
        keyExtractor={item => 'ProductHorizon_' + item.id}
        renderItem={_renderItem}
      />
    </View>
  );
};
export const ProductHorizon = React.memo(
  ProductHorizonComponent,
  (prev, next) => false,
);
const styles = StyleSheet.create({
  container: {
    marginVertical: 10,
  },
  titleContainer: {
    borderLeftColor: '#2367ff',
    borderLeftWidth: 3,
    paddingVertical: 2,
    paddingLeft: 10,
  },
  title: {
    ...globalStyles.text,
    fontWeight: '600',
    fontSize: 16,
  },
  isSub: {
    marginLeft: 8,
  },
});
