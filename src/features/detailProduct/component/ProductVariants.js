import { colors } from '@app/assets';
import { ImageReponsive } from '@app/components';
import _ from 'lodash';
import React, { useEffect, useState } from 'react';
import { FlatList, StyleSheet, View, TouchableOpacity } from 'react-native';
import { Text } from 'react-native-elements';

const VarianItems = React.memo(
  ({ item, selected, image, onPress }) => {
    if (image)
      return (
        <TouchableOpacity
          onPress={onPress}
          style={[styles.itemBox, selected ? styles.itemBoxSelected : null]}>
          <ImageReponsive
            source={{ uri: image }}
            containerStyle={styles.itemImage}
          />
        </TouchableOpacity>
      );
    return (
      <TouchableOpacity
        onPress={onPress}
        style={[styles.itemTextBox, selected ? styles.itemBoxSelected : null]}>
        <Text>{item}</Text>
      </TouchableOpacity>
    );
  },
  (prev, next) => prev.onPress === next.onPress,
);
const Component = ({ variant, onSelectVariant }) => {
  const [selectedTier1, setSelectedTier1] = useState(null);
  const [selectedTier2, setSelectedTier2] = useState(null);


  useEffect(() => {
    if (variant?.variations) {
      const mainProduct = _.find(variant?.variations, (item) => item.is_main === '1')
      const indexMainProduct = _.findIndex(variant?.variations, (item) => item.is_main === '1')
      if (mainProduct) {
        onSelectVariant(mainProduct);
      }
    }
  }, [])

  useEffect(() => {
    let id = `${selectedTier1}`;
    if (variant.config?.tier_2?.items) {
      id += '_' + selectedTier2;
    }
    if (variant.variations && variant.variations[id] && onSelectVariant) {
      onSelectVariant(variant.variations[id]);
    }
    console.log(variant.variations[id])
    // console.log(selectedTier1)
    // console.log('selectedTier2')
    // console.log(selectedTier2)
  }, [selectedTier1, selectedTier2]);

  const renderTier1 = () => {
    if (variant.config?.tier_1) {
      let listImageMaping = [];
      if (variant.config?.tier_1.images) {
        listImageMaping = _.map(variant.config?.tier_1.images, _.head);
      }
      return (
        <View style={styles.box}>
          <Text style={styles.txtParentType}>
            {variant.config?.tier_1['0']}:{' '}
            {variant.config?.tier_1?.items
              ? variant.config?.tier_1?.items[selectedTier1]
              : ''}
          </Text>
          <FlatList
            key={'productVarian_1'}
            style={styles.flatlistContainer}
            horizontal
            ItemSeparatorComponent={() => <View style={styles.itemSeparator} />}
            data={variant.config?.tier_1?.items || []}
            renderItem={({ item, index }) => (
              <VarianItems
                selected={index === selectedTier1}
                item={item}
                image={listImageMaping[index]}
                onPress={() => setSelectedTier1(index)}
              />
            )}
          />
        </View>
      );
    }
    return null;
  };

  const renderTier2 = () => {
    if (variant.config?.tier_2) {
      let listImageMaping = [];
      if (variant.config?.tier_2.images) {
        listImageMaping = _.map(variant.config?.tier_2.images, _.head);
      }
      return (
        <View style={styles.box}>
          <Text style={styles.txtParentType}>
            {variant.config?.tier_2['0']}:{' '}
            {variant.config?.tier_2?.items
              ? variant.config?.tier_2?.items[selectedTier2]
              : ''}
          </Text>
          <FlatList
            key={'productVarian_1'}
            style={styles.flatlistContainer}
            horizontal
            ItemSeparatorComponent={() => <View style={styles.itemSeparator} />}
            data={variant.config?.tier_2?.items || []}
            renderItem={({ item, index }) => (
              <VarianItems
                item={item}
                selected={index === selectedTier2}
                image={listImageMaping[index]}
                onPress={() => setSelectedTier2(index)}
              />
            )}
          />
        </View>
      );
    }
    return null;
  };

  return (
    <View style={styles.container}>
      {renderTier1()}
      {renderTier2()}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    backgroundColor: '#fff',
    marginBottom: 4,
    paddingHorizontal: 10,
    paddingVertical: 8,
  },
  box: {
    paddingBottom: 4,
  },
  txtParentType: {
    color: colors.black,
    fontSize: 14,
  },
  flatlistContainer: {
    marginTop: 15,
  },
  itemSeparator: {
    width: 15,
    backgroundColor: colors.transparent,
  },
  itemBox: {
    padding: 5,
    borderRadius: 4,
    borderColor: '#e3e3e3',
    borderWidth: 1,
  },
  itemTextBox: {
    minWidth: 62,
    minHeight: 23,
    alignItems: 'center',
    padding: 5,
    borderRadius: 4,
    backgroundColor: '#f5f5f5',
  },
  itemImage: {
    height: 38,
    width: 38,
  },
  itemBoxSelected: {
    borderRadius: 4,
    borderColor: '#0F83FF',
    borderWidth: 1,
  },
});

function areEqual(prev, next) {
  return prev.variant === next.variant;
}
export const ProductVariants = React.memo(Component, areEqual);
