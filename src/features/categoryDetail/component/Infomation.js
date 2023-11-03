import { stringHelper } from '@app/utils';
import React, { useEffect, useState } from 'react';
import { StyleSheet, View, FlatList, InteractionManager } from 'react-native';
import { Chip, Text } from 'react-native-elements';
import { globalStyles } from '@app/assets';
import api from '@app/api';
import { useQuery } from 'react-query';
import _ from 'lodash';
const fetch = async (id_category, id_brand, defaultFilter) => {
  console.log('defaultFilter', defaultFilter)
  if (id_category) {
    return await api.countCategories(id_category, defaultFilter);
  } else {
    return await api.countBrands(id_brand, defaultFilter);

  }
};

const ChipItem = React.memo(
  ({ item, isSelected, onPress }) => {
    return (
      <Chip
        title={`${item.name_vi} ${item.count > 0 ? `(${stringHelper.formatMoney(item.count)})` : ''
          }`}
        type="outline"
        containerStyle={{
          borderRadius: 36,
          backgroundColor: '#fff',
        }}
        onPress={onPress}
        buttonStyle={{ borderColor: 'transparent', alignItems: 'center' }}
        titleStyle={styles.chipItemText}
        iconRight
        icon={
          isSelected
            ? {
              name: 'ios-checkmark-circle-outline',
              type: 'ionicon',
              color: 'rgb(35, 103, 255)',
              size: 20,
            }
            : null
        }
      />
    );
  },
  (prev, next) =>
    _.isEqual(prev.item, next.item) &&
    _.isEqual(prev.isSelected, next.isSelected),
);

const component = ({
  id_category,
  id_brand,
  name,
  categories,
  onSelectCategoryId,
  defaultFilter
}) => {
  const [onReady, setOnReady] = useState(false);
  const [selectedIndex, setSelectedIndex] = useState(-1);
  const [total, setTotal] = useState(total);
  const [categoryChildren, setCategoryChildren] = useState([]);

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
      setOnReady(false);
    };
  }, []);

  const { data } = useQuery(
    ['countCategoryAndBrand', { id_category: id_category, id_brand: id_brand, ...defaultFilter }],
    () => fetch(id_category, id_brand, defaultFilter),
    { enabled: onReady },
  );

  useEffect(() => {
    if (data) {
      setTotal(data.count);
      if (data && data.items) {
        setCategoryChildren(data.items)
      }
    }
  }, [data]);

  useEffect(() => {
    if (onSelectCategoryId && id_category) {
      if (selectedIndex === -1) {
        onSelectCategoryId(id_category);
      } else {
        onSelectCategoryId(categoryChildren[selectedIndex]?.id);
      }
    }
  }, [selectedIndex]);
  return (
    <>
      <View style={{ flexDirection: 'row', margin: 10, alignItems: 'center' }}>
        <Text style={styles.titleText}>{name}</Text>
        <Text style={styles.titleQuality}>
          ({stringHelper.formatMoney(total)})
        </Text>
      </View>
      {categoryChildren && categoryChildren.length > 0 ? (
        <View>
          <FlatList
            horizontal
            maxToRenderPerBatch={3}
            initialNumToRender={5}
            removeClippedSubviews
            contentContainerStyle={{ padding: 10, backgroundColor: '#e6eef5' }}
            showsHorizontalScrollIndicator={false}
            data={categoryChildren || []}
            ItemSeparatorComponent={() => <View style={{ width: 10 }} />}
            ListHeaderComponent={() => (
              <View style={{ marginRight: 10 }}>
                <ChipItem
                  item={{ name_vi: 'Tất cả', count: 0 }}
                  isSelected={-1 === selectedIndex}
                  onPress={() => setSelectedIndex(-1)}
                />
              </View>
            )}
            renderItem={({ item, index }) => (
              <ChipItem
                item={item}
                isSelected={index === selectedIndex}
                onPress={() => setSelectedIndex(index)}
              />
            )}
          />
        </View>
      ) : null}
    </>
  );
};
export const CategoryInfomation = React.memo(
  component,
  (prev, next) => prev.id === next.id,
);

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    margin: 10,
    alignItems: 'center',
  },
  titleText: {
    ...globalStyles.text,
    fontSize: 18,
    color: '#000',
    fontWeight: '500',
    paddingRight: 5,
  },
  titleQuality: {
    ...globalStyles.text,
    fontSize: 13,
    color: '#8a8a8f',
  },
});
