import {appDimensions} from '@app/assets';
import {ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import _ from 'lodash';
import React, {useCallback, useEffect, useState} from 'react';
import {
  FlatList,
  InteractionManager,
  TouchableOpacity,
  useWindowDimensions,
  View,
} from 'react-native';
import {StyleSheet} from 'react-native';
import {Text} from 'react-native-elements';
import FastImage from 'react-native-fast-image';

const ItemChild = React.memo(
  ({item}) => {
    const {width} = useWindowDimensions();


    const handleGotoCategory = useCallback(() => {
      navigateRoute(ROUTES.CATEGORY_DETAIL, {
        id_category: item?.id || 0,
        category: item,
      });
    }, []);
    return (
      <TouchableOpacity
        style={[styles.itemContainer, {width: (width - 75) / 3}]}
        onPress={handleGotoCategory}>
        <FastImage
          source={{uri: item.icon}}
          style={styles.itemImage}
          resizeMode="contain"
        />
        <Text style={styles.itemTittle}>{item.name_vi}</Text>
      </TouchableOpacity>
    );
  },
  (prev, next) => _.isEqual(prev.item, next.item),
);
const component = ({parentCategory}) => {
  const [onReady, setOnReady] = useState(false);

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
      setOnReady(false);
    };
  }, []);

  const renderItem = ({item, index}) => {
    return <ItemChild item={item} />;
  };

  if (!onReady || _.isEmpty(parentCategory?.items)) {
    return <View style={{flex: 1, backgroundColor: '#fff'}}></View>;
  }
  return (
    <View style={{flex: 1}}>
      <FlatList
        key={'category_children'}
        ListHeaderComponent={() => (
          <TouchableOpacity
            onPress={() =>
              navigateRoute(ROUTES.CATEGORY_DETAIL, {
                id_category: parentCategory?.id,
                category: parentCategory,
              })
            }>
            <Text style={styles.txtViewAll}>Xem tất cả >>></Text>
          </TouchableOpacity>
        )}
        numColumns={3}
        columnWrapperStyle={{justifyContent: 'space-between'}}
        showsVerticalScrollIndicator={false}
        style={{backgroundColor: '#fff', flex: 1}}
        contentContainerStyle={{flexGrow: 1}}
        keyExtractor={item => 'category_children_' + item.id}
        data={parentCategory?.items || []}
        renderItem={renderItem}
      />
    </View>
  );
};
export const ChildrenCategories = React.memo(component, (prev, next) =>
  _.isEqual(prev.parentCategory, next.parentCategory),
);

const styles = StyleSheet.create({
  headerBadgeCart: {
    position: 'absolute',
    top: -5,
    right: -5,
  },
  itemContainer: {
    width: (appDimensions.width - 75) / 3,
    alignItems: 'center',
    paddingVertical: 10,
    paddingHorizontal: 4,
  },
  itemImage: {
    width: 40,
    height: 40,
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
  txtViewAll: {
    fontSize: 13,
    color: '#2367ff',
    textAlign: 'right',
    padding: 10,
  },
});
