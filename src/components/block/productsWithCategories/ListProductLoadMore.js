import {appDimensions, spacing} from '@app/assets';
import React, {useState} from 'react';
import {View, StyleSheet, FlatList, useWindowDimensions} from 'react-native';
import {ProductItem} from '../../ProductItem';
import _ from 'lodash';

const ListProductLoadMoreComponent = ({products, indexBlock}) => {
  const [layoutWidthProduct, setLayoutWidthProduct] = useState(0);
  const [ready, setReady] = useState(false);

  function onLayoutLoad(e) {
    if (e?.nativeEvent?.layout?.width > 0) {
      setLayoutWidthProduct(e?.nativeEvent?.layout?.width / 2);
      setReady(true);
    }
  }

  return (
    <>
      <View onLayout={onLayoutLoad}>
        {ready &&
          products &&
          products.length > 0 &&
          products.map((item, index) => {
            if (index % 2 === 0) {
              return (
                <View style={styles.flatlist_columnStyle}>
                  <ProductItem
                    key={`productcategory_${indexBlock}_${products[index]?.id}_${index}`}
                    product={products[index]}
                    containerStyle={{
                      flex: 1,
                      backgroundColor: '#fff',
                      width: layoutWidthProduct - spacing.tiny / 2,
                      marginRight: spacing.tiny / 2,
                    }}
                  />

                  <ProductItem
                    key={`productcategory_${indexBlock}_${
                      products[index + 1]?.id
                    }_${index}`}
                    product={products[index + 1]}
                    containerStyle={{
                      flex: 1,
                      backgroundColor: '#fff',
                      width: layoutWidthProduct - spacing.tiny / 2,
                      marginLeft: spacing.tiny / 2,
                    }}
                  />
                </View>
              );
            }
            return null;
          })}
      </View>
    </>
    // <FlatList
    //   listKey={(item, index) => {
    //     return `ListProductLoadMore_component__key_${indexBlock}_${item.id.toString()}`;
    //   }}
    //   scrollEnabled={false}
    //   key={`ListProductLoadMore_component__key_${indexBlock}`}
    //   keyExtractor={item => `productcategory_1_${indexBlock}_${item.id}`}
    //   style={styles.flatListContainer}
    //   numColumns={2}
    //   keyboardDismissMode="on-drag"
    //   columnWrapperStyle={styles.flatlist_columnStyle}
    //   ListFooterComponent={footer}
    //   showsHorizontalScrollIndicator={false}
    //   data={products || []}
    //   renderItem={({item}) => (
    //     <ProductItem
    //       product={item}
    //       containerStyle={{
    //         backgroundColor: '#fff',
    //         width: width / 2 - spacing.small,
    //         minHeight: 240,
    //       }}
    //     />
    //   )}
    // />
  );
};

function areEqual(prevProps, nextProps) {
  return (
    _.isEqual(prevProps.products, nextProps.products) &&
    _.isEqual(prevProps.footer, nextProps.footer)
  );
}
export const ListProductLoadMore = React.memo(
  ListProductLoadMoreComponent,
  areEqual,
);
const styles = StyleSheet.create({
  flatlist_columnStyle: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: spacing.tiny,
  },
  separator: {
    width: spacing.small,
  },
});
