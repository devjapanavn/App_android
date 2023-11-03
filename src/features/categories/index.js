import api from '@app/api';
import { colors } from '@app/assets';
import { useIsFocused } from '@react-navigation/native';
import React, { useEffect, useLayoutEffect, useState } from 'react';
import { InteractionManager, View } from 'react-native';
import { StyleSheet } from 'react-native';
import { Searchbar } from 'react-native-paper';
import { useQuery } from 'react-query';
import {
  ChildrenCategories,
  HeaderCategory,
  ParentCategories,
} from './component';

const fetch = async () => {
  return await api.getCategories(0, 1);
};

const Screen = props => {
  const isFocus = useIsFocused();
  const [onReady, setOnReady] = useState(false);
  const [parentSelected, setParentSelected] = useState(null);
  const [listCategory, setListCategory] = useState([]);

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
      setOnReady(false);
    };
  }, []);


  const { status, data, error, refetch } = useQuery(
    ['getListCategory'],
    fetch,
    {
      enabled: isFocus && onReady,
    },
  );

  useEffect(() => {
    if (data && data.length > 0) {
      setListCategory(data);
    }
  }, [data]);

  if (!isFocus) {
    return <View />
  }

  return (
    <View style={styles.container}>
      <HeaderCategory />
      <View style={{ flexDirection: 'row', flex: 1 }}>
        <ParentCategories onSelectedCategory={setParentSelected} categories={listCategory} />
        <ChildrenCategories parentCategory={parentSelected} />
      </View>
    </View>
  );
};

export const CategoriesScreen = Screen;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.white,
  },
});
