import {spacing} from '@app/assets';
import React, {useState, useEffect} from 'react';
import {View, StyleSheet} from 'react-native';
import {Button} from 'react-native-elements';
import _ from 'lodash';
import TabCategories from './tabCategories';
import api from '@app/api';
import {ListProductLoadMore} from './ListProductLoadMore';
import {ListProductSlide} from './ListProductSlide';
import {stringHelper} from '@app/utils';
import {BLOCK_PRODUCT_TYPE} from '@app/constants';

const ProductWithCategoriesComponent = ({
  indexBlock,
  menus,
  type,
  showStype,
  header,
}) => {
  const [menuTabs, setMenuTabs] = useState([]);
  const [selectedMenuIndex, setselectedMenuIndexMenus] = useState(0);
  const [isLoadingMore, setLoadingMore] = useState(false);
  const [listProduct, setListProduct] = useState([]);
  const styleContainer = {
    backgroundColor: showStype?.color_background || undefined,
    marginTop: stringHelper.formatToNumber(showStype?.margin?.top) || undefined,
    marginLeft:
      stringHelper.formatToNumber(showStype?.margin?.left) || undefined,
    marginRight:
      stringHelper.formatToNumber(showStype?.margin?.right) || undefined,
    marginBottom:
      stringHelper.formatToNumber(showStype?.margin?.bottom) || undefined,

    paddingLeft:
      stringHelper.formatToNumber(showStype?.padding?.left) || undefined,
    paddingRight:
      stringHelper.formatToNumber(showStype?.padding?.right) || undefined,
    paddingBottom:
      stringHelper.formatToNumber(showStype?.padding?.bottom) || undefined,
    paddingTop:
      stringHelper.formatToNumber(showStype?.padding?.top) || undefined,
  };

  useEffect(() => {
    if (menus && menus.length > 0) {
      setMenuTabs(
        menus.map(mn => {
          mn.page = 1;
          return mn;
        }),
      );
      getProductOnIndexChange();
    }
  }, [menus]);

  useEffect(() => {
    getProductOnIndexChange();
  }, [selectedMenuIndex]);

  function getProductOnIndexChange() {
    if (menus && menus[selectedMenuIndex]) {
      const selectedMenus = menus[selectedMenuIndex];
      const products = menus[selectedMenuIndex]?.products || [];
      console.log('selectedMenus', selectedMenus.loai);

      if (selectedMenus) {
        if (selectedMenus.loai === 3) {
          setListProduct(
            _.take(products, selectedMenus.load_more?.param?.pages?.limit || 6),
          );
        } else {
          setListProduct(products);
        }
      }
    }
  }

  async function onLoadMore() {
    const selectedMenus = menuTabs[selectedMenuIndex];
    if (selectedMenus) {
      if (selectedMenus.loai === 3) {
        const productsMenus = menus[selectedMenuIndex]?.products || [];
        const limit = selectedMenus.load_more?.param?.pages?.limit || 6;
        const products = _.take(_.drop(productsMenus, limit), limit);
        const newData = _.map(menuTabs, (menu, index) => {
          if (index === selectedMenuIndex) {
            menu.page = menu.page + 1;
          }
          return menu;
        });
        setMenuTabs(newData);
        setListProduct(prev => [...prev, ...products]);
      } else {
        setLoadingMore(true);
        const res = await api.searchProducts({
          id_category: selectedMenus.load_more?.param?.id_category,
          page: selectedMenus.page + 1,
          sort: selectedMenus.load_more?.param?.sort,
          limit: selectedMenus.load_more?.param?.pages?.limit,
        });
        setLoadingMore(false);
        if (res && res.list && res.list.length > 0) {
          const newData = _.map(menuTabs, (menu, index) => {
            if (index === selectedMenuIndex) {
              menu.page = menu.page + 1;
            }
            return menu;
          });
          setListProduct(prev => [...prev, ...res.list]);
          setMenuTabs(newData);
        }
      }
    }
  }

  const LoadMoreIcon = React.memo(
    () => {
      if (
        menuTabs[selectedMenuIndex]?.load_more?.param?.pages?.totalPage > 1 &&
        menuTabs[selectedMenuIndex].page <
          menuTabs[selectedMenuIndex].load_more?.param.pages.totalPage
      )
        return (
          <Button
            type={'clear'}
            title="Xem thêm "
            loading={isLoadingMore}
            disabled={isLoadingMore}
            titleStyle={styles.loadmore_title}
            iconRight
            onPress={onLoadMore}
            icon={{
              type: 'ionicon',
              name: 'chevron-down-sharp',
              size: 15,
              color: '#0f83ff',
            }}
          />
        );
      return <View />;
    },
    () => false,
  );

  const _renderListProducts = () => {
    switch (type) {
      case BLOCK_PRODUCT_TYPE.LOAD_MORE:
        return (
          <>
            <ListProductLoadMore
              indexBlock={indexBlock}
              products={listProduct}
            />
            {listProduct && listProduct.length > 0 ? <LoadMoreIcon /> : null}
          </>
        );

      case BLOCK_PRODUCT_TYPE.SCROLL:
        return (
          <ListProductSlide indexBlock={indexBlock} products={listProduct} />
        );
      default:
        <View />;
    }
  };
  return (
    <View style={styleContainer}>
      {header ? header() : null}
      {menuTabs && menuTabs.length > 1 ? (
        <TabCategories
          onChangeTab={setselectedMenuIndexMenus}
          categories={menuTabs}
          rootIndex={indexBlock}
          showStype={showStype}
        />
      ) : null}
      {_renderListProducts()}
    </View>
  );
};

function areEqual(prevProps, nextProps) {
  return _.isEqual(prevProps.menus, nextProps.menus);
}
export const ProductWithCategories = React.memo(
  ProductWithCategoriesComponent,
  areEqual,
);
const styles = StyleSheet.create({
  flatListContainer: {padding: spacing.medium},
  flatlist_columnStyle: {
    justifyContent: 'space-between',
    marginBottom: spacing.medium,
  },
  loadmore_title: {
    color: '#0f83ff',
    fontSize: 12,
    fontFamily: 'SF Pro Display',
  },
  separator: {
    width: spacing.small,
  },
});
