import api from '@app/api';
import { colors, globalStyles, spacing } from '@app/assets';
import _ from 'lodash';
import React, { useEffect, useState } from 'react';
import { StatusBar, StyleSheet, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useQuery } from 'react-query';
import { HeaderSearch, ListSearch } from './component';
import { useIsFocused } from '@react-navigation/native';
import Spinner from 'react-native-spinkit';

const Screen = props => {
  const isFocused = useIsFocused();
  const [dataSearch, setDataSearch] = useState({
    pages: null,
    suggestion: [],
    bestseller: [],
    list: [],
  });

  const [filter, setFilter] = useState({
    text_search: '',
    page: 1,
  });

  useEffect(() => {
    if (_.isEmpty(filter.text_search)) {
      setDataSearch({ pages: null, suggestion: [], bestseller: [], list: [] });
    }
  }, [filter.text_search]);

  function onChangeKeyWord(value) {
    setFilter(prevState => ({ ...prevState, text_search: value, page: 1 }));
  }

  const searchProduct = async () => {
    return await api.searchProducts(filter);
  };

  const { data, status, isLoading } = useQuery(
    ['searchProduct', filter],
    searchProduct,
    {
      enabled: !_.isEmpty(filter.text_search),
    },
  );

  useEffect(() => {
    if (data) {
      if (filter.page === 1) {
        setDataSearch(data);
      } else {
        setDataSearch(prevState => ({
          ...prevState,
          list: [...prevState.list, ...data.list],
          bestseller: [...prevState.bestseller, ...data.bestseller],
        }));
      }
    } else if (!data && filter.page === 1) {
      setDataSearch({ list: [], bestseller: [], suggestion: [] });
    }
  }, [data]);

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

  if (isFocused) {
    return (
      <SafeAreaView style={styles.container}>
        <StatusBar barStyle="light-content" backgroundColor={colors.primary} />
        <HeaderSearch onSearch={onChangeKeyWord} />
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
          <ListSearch
            {...dataSearch}
            onLoadMore={onLoadMore}
            keyword={filter.text_search}
          />
        )}
      </SafeAreaView>
    );
  }
  return <View />;
};

export const SearchScreen = Screen;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.white,
  },
  titleText: {
    ...globalStyles.text,
    fontSize: 16,
    fontWeight: '500',
  },
});
