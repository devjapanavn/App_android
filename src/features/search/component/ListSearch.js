import {appDimensions, globalStyles, spacing} from '@app/assets';
import _ from 'lodash';
import React from 'react';
import {FlatList} from 'react-native';
import {StyleSheet, View} from 'react-native';
import {ProductSearchItem} from './ProductSearchItem';
import {ProductItem} from '@app/components';
import {navigateRoute} from '@app/route';
import {stringHelper} from '@app/utils';
import {ROUTES} from '@app/constants';
import {Button, Text} from 'react-native-elements';
import {SuggestionHeader} from './SuggestionHeader';

const component = ({
  pages,
  suggestion,
  bestseller,
  list,
  onLoadMore,
  keyword,
}) => {
  renderFooter = () => {
    if (isLoadMore) {
      return (
        <View
          style={{
            justifyContent: 'center',
            alignItems: 'center',
            margin: spacing.large,
          }}>
          <Spinner type="ThreeBounce" color={colors.primary} size={40} />
        </View>
      );
    }
    return null;
  };
  
  const renderHeaderList = () => {
    if (list && list.length > 0 && pages) {
      console.log('pages.total_item', pages);
      return (
        <View style={styles.headerSection}>
          <Text style={styles.headerText}>
            {stringHelper.formatMoney(pages.total_item)} kết quả phù hợp
          </Text>
          <Button
            onPress={() => navigateRoute(ROUTES.SEARCH_ALL, {keyword: keyword})}
            title="Xem tất cả >>>"
            titleStyle={styles.headerButton}
            type="clear"
          />
        </View>
      );
    }
    return null;
  };

  if (list && list.length > 0) {
    return (
      <FlatList
        key={'search_list'}
        stickyHeaderIndices={[0]}
        removeClippedSubviews={true}
        showsVerticalScrollIndicator={false}
        ListHeaderComponent={renderHeaderList}
        keyExtractor={item => `search_list${item.id}`}
        data={list}
        getItemLayout={(data, index) => ({
          length: 100,
          offset: 100 * index,
          index,
        })}
        onEndReachedThreshold={0.7}
        onEndReached={onLoadMore}
        renderItem={({item}) => (
          <ProductSearchItem
            product={item}
            extraParams={{direction: 'search', keyword}}
          />
        )}
      />
    );
  } else if (bestseller && bestseller.length > 0) {
    return (
      <FlatList
        key={'bestseller_list'}
        numColumns={2}
        contentContainerStyle={{margin: spacing.medium}}
        ItemSeparatorComponent={() => <View style={{width: spacing.small}} />}
        columnWrapperStyle={{
          justifyContent: 'space-between',
          marginBottom: spacing.medium,
        }}
        removeClippedSubviews={true}
        showsVerticalScrollIndicator={false}
        ListHeaderComponent={() => <SuggestionHeader suggestion={suggestion} />}
        keyExtractor={item => `bestseller_list${item.id}`}
        data={bestseller}
        onEndReachedThreshold={0.7}
        onEndReached={onLoadMore}
        renderItem={({item}) => (
          <ProductItem
            product={item}
            containerStyle={{
              width: appDimensions.width / 2 - spacing.small * 2,
            }}
          />
        )}
      />
    );
  }
  return <View />;
};
export const ListSearch = React.memo(
  component,
  (prev, next) =>
    _.isEqual(prev.suggestion, next.suggestion) &&
    _.isEqual(prev.bestseller, next.bestseller) &&
    _.isEqual(prev.pages, next.pages) &&
    _.isEqual(prev.list, next.list),
);

const styles = StyleSheet.create({
  headerSection: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    backgroundColor: '#fff',
    elevation: 2,
    padding: 10,
    alignItems: 'center',
  },
  headerText: {
    ...globalStyles.text,
    fontSize: 14,
    color: '#3b4859',
    fontWeight: '500',
  },
  headerButton: {
    ...globalStyles.text,
    fontSize: 13,
    color: '#2367ff',
  },
});
