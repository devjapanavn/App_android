import {appDimensions, spacing} from '@app/assets';
import {ProductItem} from '@app/components';
import _ from 'lodash';
import React from 'react';
import {FlatList} from 'react-native';
import {StyleSheet, View} from 'react-native';

const component = ({products, onLoadMore, keyword}) => {
  return (
    <FlatList
      numColumns={2}
      showsVerticalScrollIndicator={false}
      keyExtractor={item => `search_all_product_${item.id}`}
      data={products}
      columnWrapperStyle={{
        margin: spacing.medium,
        justifyContent: 'space-between',
        marginBottom: spacing.medium,
      }}
      onEndReachedThreshold={0.7}
      onEndReached={onLoadMore}
      ItemSeparatorComponent={() => <View style={{width: spacing.small}} />}
      renderItem={({item}) => (
        <ProductItem
          extraParams={{direction: 'search', keyword}}
          product={item}
          containerStyle={{
            width: appDimensions.width / 2 - spacing.small * 2,
          }}
        />
      )}
    />
  );
};
export const ListSearch = React.memo(component, (prev, next) =>
  _.isEqual(prev.products, next.products),
);

const styles = StyleSheet.create({});
