import api from '@app/api';
import {ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import {useIsFocused} from '@react-navigation/native';
import _ from 'lodash';
import React, {useEffect, useState} from 'react';
import {
  FlatList,
  View,
  InteractionManager,
  TouchableOpacity,
} from 'react-native';
import {StyleSheet} from 'react-native';
import {Text} from 'react-native-elements';
import FastImage from 'react-native-fast-image';
import {useQuery} from 'react-query';
const ItemCategory = React.memo(
  ({category, isSelected, onPress}) => {
    return (
      <TouchableOpacity
        onPress={onPress}
        style={[styles.itemContainer, isSelected ? styles.itemSelected : null]}>
        <FastImage
          source={{uri: category.icon}}
          style={styles.itemImage}
          resizeMode="contain"
        />
        <Text style={styles.itemTittle}>{category.name_vi}</Text>
      </TouchableOpacity>
    );
  },
  (prev, next) =>
    _.isEqual(prev.category, next.category) &&
    prev.isSelected === next.isSelected,
);

const component = ({categories, onSelectedCategory}) => {
  const [onReady, setOnReady] = useState(false);
  const [selectedIndex, setSelectedIndex] = useState(0);

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
      setOnReady(false);
    };
  }, []);

  useEffect(() => {
    if (categories && categories.length > 0 && onSelectedCategory) {
      onSelectedCategory(categories[selectedIndex]);
    }
  }, [selectedIndex, categories]);

  function onPressParentCat(item, index) {
    if (item && item.items && item.items.length > 0) {
      setSelectedIndex(index);
    } else {
      navigateRoute(ROUTES.CATEGORY_DETAIL, {
        id_category: item?.id,
        category: item,
      });
    }
  }
  const renderItem = ({item, index}) => {
    return (
      <ItemCategory
        category={item}
        isSelected={index === selectedIndex}
        onPress={() => onPressParentCat(item, index)}
      />
    );
  };

  if (!onReady) {
    return <View style={{width: 75, backgroundColor: '#fff'}}></View>;
  }

  return (
    <View style={{width: 75, elevation: 1}}>
      <FlatList
        removeClippedSubviews={true}
        showsVerticalScrollIndicator={false}
        style={{backgroundColor: '#f5f8ff', flex: 1}}
        contentContainerStyle={{flexGrow: 1}}
        data={categories || []}
        renderItem={renderItem}
      />
    </View>
  );
};
export const ParentCategories = React.memo(component, (prev, next) =>
  _.isEqual(prev.categories, next.categories),
);

const styles = StyleSheet.create({
  headerBadgeCart: {
    position: 'absolute',
    top: -5,
    right: -5,
  },
  itemContainer: {
    width: 75,
    marginHorizontal: 5,
    alignItems: 'center',
    paddingVertical: 10,
  },
  itemImage: {
    width: 35,
    height: 35,
  },
  itemTittle: {
    textAlign: 'center',
    paddingHorizontal: 4,
    paddingTop: 4,
    fontSize: 12,
  },
  itemSelected: {
    borderBottomColor: '#2367ff',
    borderBottomWidth: 3,
  },
});
