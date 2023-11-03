import api from '@app/api';
import { colors, images, spacing } from '@app/assets';
import { FilterDrawer } from '@app/components';
import { stringHelper } from '@app/utils';
import { useIsFocused, useRoute } from '@react-navigation/native';
import _ from 'lodash';
import React, { useCallback, useEffect, useRef, useState } from 'react';
import { DrawerLayoutAndroid, InteractionManager, useWindowDimensions } from 'react-native';
import { StatusBar, StyleSheet, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Spinner from 'react-native-spinkit';
import { useQuery } from 'react-query';
import { FilterTab, HeaderSearch, ListSearch, ModalFilter } from './component';

const Screen = () => {
  const route = useRoute();
  const { width } = useWindowDimensions();
  const isFocused = useIsFocused();
  const [onReady, setOnReady] = useState(false);
  const [modalFilterVisible, setModalFilterVisible] = useState(false);
  const drawer = useRef(null);

  const [filter, setFilter] = useState({
    text_search: route.params?.keyword,
    page: 1,
    category_check: "",
    brand_check: '',
  });
  const [dataSearch, setDataSearch] = useState({
    pages: null,
    list: [],
  });

  const searchProduct = async () => {
    return await api.searchProducts(filter);
  };

  const { data, status, isLoading } = useQuery(
    ['searchProduct', filter],
    searchProduct,
    {
      enabled: !_.isEmpty(route.params?.keyword) && isFocused,
    },
  );

  const handleToggleFilter = useCallback(() => {
    setModalFilterVisible(prev => !prev);
  }, []);

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  useEffect(() => {
    if (data) {
      if (filter.page === 1) {
        setDataSearch(data);
      } else {
        setDataSearch(prevState => ({
          ...prevState,
          list: [...prevState.list, ...data.list],
        }));
      }
    } else if (!data && filter.page === 1) {
      setDataSearch({ list: [] });
    }
  }, [data]);

  function onChangeType(type) {
    switch (type) {
      case 0:
        setFilter(prev => ({ ...prev, text_search: route.params?.keyword, page: 1 }));
        break;
      case 1:
        setFilter(prev => ({ ...prev, text_search: route.params?.keyword, page: 1, sale: 1 }));
        break;
      case 2:
        setFilter(prev => ({ ...prev, text_search: route.params?.keyword, page: 1, sort: 'pza' }));
        break;
      case 3:
        setFilter(prev => ({ ...prev, text_search: route.params?.keyword, page: 1, sort: 'paz' }));
        break;
      default:
        setFilter(prev => ({ ...prev, Ftext_search: route.params?.keyword, page: 1 }));
        break;
    }
  }

  function onLoadMore() {
    if (
      dataSearch.pages &&
      dataSearch.pages.current_page &&
      dataSearch.pages.totalPage &&
      dataSearch.pages.current_page < dataSearch.pages.totalPage
    ) {
      setFilter(prev => ({ ...prev, page: prev.page + 1 }));
    }
  }
  const navigationView = () => (
    <FilterDrawer
      onClose={() => drawer.current.closeDrawer()}
      textSearch={filter.text_search}
      onFilter={filterData =>
        setFilter({
          ...filterData,
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
        <HeaderSearch keyword={route.params?.keyword} />
        {isFocused && onReady ? (
          <>
            <FilterTab
              openFilter={() => drawer.current.openDrawer()}
              onChangeFilterType={onChangeType}
            />
            {isLoading && filter.page === 1 ? (
              <View
                style={{
                  justifyContent: 'center',
                  alignItems: 'center',
                  margin: spacing.large,
                }}>
                <Spinner type="ThreeBounce" color={colors.primary} size={40} />
              </View>
            ) : (
              <ListSearch products={dataSearch.list} onLoadMore={onLoadMore} keyword={filter.text_search}/>
            )}
          </>
        ) : (
          <View />
        )}
      </DrawerLayoutAndroid>
      <ModalFilter visible={modalFilterVisible} onClose={handleToggleFilter} />
    </SafeAreaView>
  );
};

export const SearchAllScreen = Screen;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.white,
  },
});
