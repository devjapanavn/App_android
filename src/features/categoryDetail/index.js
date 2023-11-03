import { appDimensions, colors, spacing } from '@app/assets';
import {
  FilterDrawer,
  HeaderComponent,
  ProductItem,
  FooterTab,
  PopupNoti,
} from '@app/components';
import React, { useEffect, useRef, useState } from 'react';
import {
  StatusBar,
  StyleSheet,
  InteractionManager,
  View,
  FlatList,
  DrawerLayoutAndroid,
  useWindowDimensions,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { globalStyles } from '@app/assets';
import Animated, {
  runOnJS,
  useAnimatedScrollHandler,
  useSharedValue,
} from 'react-native-reanimated';
import { useIsFocused, useRoute } from '@react-navigation/native';
import api from '@app/api';
import { useQuery } from 'react-query';
import { CategoryInfomation, FilterTab, ListBlocks } from './component';
import Spinner from 'react-native-spinkit';
import { stringHelper } from '@app/utils';
const AnimatedFlatList = Animated.createAnimatedComponent(FlatList);
const Screen = props => {
  const { width } = useWindowDimensions();
  const isFocused = useIsFocused();
  const drawer = useRef(null);
  const route = useRoute();
  const [onReady, setOnReady] = useState(false);
  const contentOffset = useSharedValue(0);
  const [dataSearch, setDataSearch] = useState({
    pages: null,
    list: [],
  });
  const [blocks, setBlocks] = useState([]);
  const [filter, setFilter] = useState({
    id_category: route.params?.id_category || 0,
    id_brand: route.params?.id_brand || 0,
    category_check: route.params?.defaultFilter?.category
      ? route.params?.defaultFilter?.category.join(',')
      : '' || '',
    brand_check: route.params?.defaultFilter?.brand
      ? route.params?.defaultFilter?.brand.join('')
      : '' || '',
    beginMinPrice: route.params?.defaultFilter?.beginMinPrice || '',
    endMaxPrice: route.params?.defaultFilter?.endMaxPrice || '',
    page: 1,
  });

  const handleScroll = useAnimatedScrollHandler(event => {
    contentOffset.value = event.contentOffset.y;
    const isNearEndBottom =
      event.layoutMeasurement.height + event.contentOffset.y >=
      event.contentSize.height - 50;
    if (isNearEndBottom) {
      runOnJS(onLoadMore)();
    }
  });

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  const searchProduct = async () => {
    return await api.searchProducts(filter);
  };

  const { data, status, isLoading } = useQuery(
    [
      'searchCategoryProduct',
      filter,
      {
        id_category: route.params?.id_category,
        id_brand: route.params?.id_brand,
      },
    ],
    searchProduct,
    {
      enabled: isFocused && onReady,
    },
  );

  useEffect(() => {
    if (data && data.list) {
      if (filter.page === 1) {
        setDataSearch(data);
      } else {
        setDataSearch(prevState => ({
          ...prevState,
          list: [...prevState.list, ...data.list],
        }));
      }
    } else if (!data?.list && filter.page === 1) {
      setDataSearch({ list: [] });
    }
    if (data && data.list_block && filter.page === 1) {
      setBlocks(data.list_block);
    }
  }, [data]);

  function onChangeSelectedCategory(id) {
    setFilter(prev => ({ ...prev, page: 1, id_category: id }));
  }

  function onChangeType(type) {
    switch (type) {
      case 0:
        setFilter(prev => ({
          ...prev,
          page: 1,
          sale: 0,
          sort: 'default',
        }));
        break;
      case 1:
        setFilter(prev => ({
          ...prev,
          page: 1,
          sale: 1,
          sort: 'default',
        }));
        break;
      case 2:
        setFilter(prev => ({
          ...prev,
          page: 1,
          sale: 0,
          sort: 'pza',
        }));
        break;
      case 3:
        setFilter(prev => ({
          ...prev,
          page: 1,
          sale: 0,
          sort: 'paz',
        }));
        break;
      default:
        setFilter(prev => ({
          ...prev,
          page: 1,
          sale: 0,
          sort: 'default',
        }));
        break;
    }
  }

  function onLoadMore() {
    if (
      !isLoading &&
      dataSearch.pages &&
      dataSearch.pages.current_page &&
      dataSearch.pages.totalPage &&
      filter.page < dataSearch.pages.totalPage
    ) {
      setFilter(prev => ({ ...prev, page: prev.page + 1 }));
    }
  }

  function _renderHeader() {
    return (
      <>
        <ListBlocks blocks={blocks} />
        <CategoryInfomation
          categories={route.params?.category?.items || []}
          id_category={route.params?.id_category}
          id_brand={route.params?.id_brand}
          name={route.params?.category?.name_vi || route.params?.brand?.name_vi}
          onSelectCategoryId={onChangeSelectedCategory}
          defaultFilter={route.params?.defaultFilter}
        />

        <FilterTab
          onChangeFilterType={onChangeType}
          openFilter={() => drawer.current.openDrawer()}
        />
      </>
    );
  }

  function _renderEmpty() {
    if (isLoading && filter.page === 1) {
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
    return <View />;
  }

  const navigationView = () => (
    <FilterDrawer
      onClose={() => drawer.current.closeDrawer()}
      categoryId={stringHelper.formatToNumber(route.params?.id_category)}
      brandId={stringHelper.formatToNumber(route.params?.id_brand)}
      defaultFilter={route.params?.defaultFilter}
      onFilter={filterData =>
        setFilter({
          ...filterData,
          id_category: route.params?.id_category,
          id_brand: route.params?.id_brand,
          page: 1,
        })
      }
    />
  );

  return (
    <SafeAreaView style={styles.container}>
      <DrawerLayoutAndroid
        ref={drawer}
        drawerLockMode="locked-open"
        drawerWidth={width}
        drawerPosition={'right'}
        renderNavigationView={navigationView}>
        <StatusBar barStyle="light-content" />
        <HeaderComponent offsetY={contentOffset} showBack={true} />
        {!onReady && isLoading ? (
          <View
            style={{
              justifyContent: 'center',
              alignItems: 'center',
              margin: spacing.large,
              flex: 1,
            }}>
            <Spinner type="Circle" color={colors.primary} size={40} />
          </View>
        ) : (
          <AnimatedFlatList
            onScroll={handleScroll}
            removeClippedSubviews={true}
            windowSize={16}
            ListHeaderComponent={_renderHeader()}
            numColumns={2}
            keyExtractor={item =>
              `category_${filter.id_category}_product_${item.id}`
            }
            data={dataSearch.list}
            columnWrapperStyle={{
              justifyContent: 'space-between',
              margin: spacing.small,
            }}
            onEndReachedThreshold={0.7}
            onEndReached={onLoadMore}
            ItemSeparatorComponent={() => (
              <View style={{ width: spacing.small }} />
            )}
            ListEmptyComponent={_renderEmpty()}
            renderItem={({ item }) => (
              <ProductItem
                product={item}
                containerStyle={{
                  width: appDimensions.width / 2 - spacing.small * 2,
                }}
              />
            )}
          />
        )}
      </DrawerLayoutAndroid>
      <FooterTab />
      <PopupNoti type={3} />
    </SafeAreaView>
  );
};

export const CategoryDetailScreen = Screen;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.white,
  },
  m_10: {
    marginVertical: 10,
  },

  chipItemText: {
    ...globalStyles.text,
    fontSize: 13,
    color: '#3b4859',
    padding: 5,
  },
  btnGroup: {
    flex: 1,
    padding: 0,
    marginHorizontal: 0,
    marginVertical: 0,
    borderColor: 'transparent',
  },
  btnFilterIcon: {
    flex: 0,
    marginVertical: 5,
    borderLeftWidth: 0.5,
    borderLeftColor: '#888',
    paddingHorizontal: 15,
    paddingVertical: 10,
  },
});
