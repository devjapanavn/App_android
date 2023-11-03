import {globalStyles, spacing} from '@app/assets';
import { stringHelper } from '@app/utils';
import React from 'react';
import {FlatList, StyleSheet, Text, TouchableOpacity, View} from 'react-native';
import FastImage from 'react-native-fast-image';

const ItemIcon = React.memo(
  ({item, onPress}) => {
    return (
      <TouchableOpacity
        activeOpacity={0.9}
        onPress={onPress}
        style={styles.itemContainer}>
        <FastImage
          source={{uri: item.images_mobile}}
          style={[styles.item]}
          resizeMode="contain"
        />
        <Text style={[styles.itemTitle]}>{item.alt}</Text>
      </TouchableOpacity>
    );
  },
  () => true,
);
const component = ({icons, onPress,showStype}) => {
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


  const _renderItem = ({item}) => {
    return <ItemIcon item={item} onPress={() => onPress(item)} />;
  };

  return (
    <View>
      <FlatList
        style={[styles.container,styleContainer]}
        horizontal
        ItemSeparatorComponent={() => <View style={{width: spacing.medium}} />}
        showsHorizontalScrollIndicator={false}
        data={icons}
        renderItem={_renderItem}
      />
    </View>
  );
};
export const IconEvent = React.memo(component, () => true);

const styles = StyleSheet.create({
  container: {
    padding: spacing.small,
  },
  itemContainer: {
    width: 70,
    alignItems: 'center',
  },
  itemTitle: {
    ...globalStyles.text,
    textAlign: 'center',
    color: '#2a2a2a',
    fontSize: 12,
    letterSpacing: 0,
    marginTop: spacing.small,
  },
  item: {
    width: 46,
    height: 46,
  },
});
