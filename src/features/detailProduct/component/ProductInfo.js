import {colors} from '@app/assets';
import {onPressLink, stringHelper} from '@app/utils';
import _ from 'lodash';
import React from 'react';
import {FlatList, Linking, StyleSheet, View} from 'react-native';
import {Text, Button, ListItem} from 'react-native-elements';

const Component = ({detail}) => {
  return (
    <View style={styles.box}>
      <Text style={styles.headerTitleStyle}>Thông tin chung</Text>
      <ListItem bottomDivider containerStyle={styles.itemContainer}>
        <ListItem.Content>
          <ListItem.Title style={styles.itemText}>SKU</ListItem.Title>
        </ListItem.Content>
        <ListItem.Title style={styles.itemText}>
          {detail.sku || '--'}
        </ListItem.Title>
      </ListItem>
      <ListItem bottomDivider containerStyle={styles.itemContainer}>
        <ListItem.Content>
          <ListItem.Title style={styles.itemText}>GTIN</ListItem.Title>
        </ListItem.Content>
        <ListItem.Title style={styles.itemText}>
          {detail.gtin || '--'}
        </ListItem.Title>
      </ListItem>
      <ListItem
        bottomDivider
        containerStyle={styles.itemContainer}
        disabled={!detail.brand_link}
        onPress={() => onPressLink(detail.brand_link)}>
        <ListItem.Content>
          <ListItem.Title style={styles.itemText}>Thương hiệu</ListItem.Title>
        </ListItem.Content>
        <ListItem.Title
          style={[
            styles.itemText,
            detail.brand_link
              ? {
                  color: colors.link,
                  fontWeight:'bold'
                }
              : null,
          ]}>
          {detail.brand || '--'}
        </ListItem.Title>
      </ListItem>
      <ListItem bottomDivider containerStyle={styles.itemContainer}>
        <ListItem.Content>
          <ListItem.Title style={styles.itemText}>Xuất xứ</ListItem.Title>
        </ListItem.Content>
        <ListItem.Title style={styles.itemText}>
          {detail.country || '--'}
        </ListItem.Title>
      </ListItem>
      <ListItem bottomDivider containerStyle={styles.itemContainer}>
        <ListItem.Content>
          <ListItem.Title style={styles.itemText}>Sản xuất tại</ListItem.Title>
        </ListItem.Content>
        <ListItem.Title style={styles.itemText}>
          {detail.made_in || '--'}
        </ListItem.Title>
      </ListItem>
      <ListItem bottomDivider containerStyle={styles.itemContainer}>
        <ListItem.Content>
          <ListItem.Title style={styles.itemText}>Quy cách</ListItem.Title>
        </ListItem.Content>
        <ListItem.Title style={styles.itemText}>
          {detail.style || '--'}
        </ListItem.Title>
      </ListItem>
    </View>
  );
};

const styles = StyleSheet.create({
  box: {
    marginVertical: 4,
    padding: 10,
    backgroundColor: '#fff',
  },
  headerTitleStyle: {
    fontSize: 16,
    color: '#000',
    fontWeight: '500',
    flex: 1,
    marginBottom: 8,
  },
  itemText: {
    fontSize: 13,
    lineHeight: 18,
  },
  itemContainer: {
    paddingLeft: 0,
  },
});

function areEqual(prev, next) {
  return _.isEqual(prev.detail, next.detail);
}
export const ProductInfo = React.memo(Component, areEqual);
