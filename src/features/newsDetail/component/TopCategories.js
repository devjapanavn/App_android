import {globalStyles} from '@app/assets';
import React from 'react';
import {StyleSheet} from 'react-native';
import {FlatList, View} from 'react-native';
import {Text} from 'react-native-elements';
import {width} from 'react-native/Libraries/DeprecatedPropTypes/DeprecatedLayoutPropTypes';
const categories = [
  {id: 1, name: 'Top Trending'},
  {id: 2, name: 'Văn hóa Nhật Bản'},
  {id: 3, name: 'Dinh dưỡng và sức khỏe'},
  {id: 4, name: 'Chuyện phòng the'},
];
const Component = () => {
  const renderItem = ({item, index}) => {
    return (
      <View style={styles.itemContainer}>
        <Text style={styles.itemText}>{item.name}</Text>
      </View>
    );
  };
  return (
    <View>
      <FlatList
        horizontal
        data={categories}
        keyExtractor={item => `news_detail_top_category_${item.id}`}
        renderItem={renderItem}
        ItemSeparatorComponent={() => <View style={styles.separator}></View>}
      />
    </View>
  );
};
export const TopCategories = React.memo(Component, (prev, next) => true);
const styles = StyleSheet.create({
  separator: {
    width: 0.3,
    backgroundColor: '#fff',
  },
  itemContainer: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    backgroundColor: '#ffa200',
  },
  itemText: {
    ...globalStyles.text,
    color: '#fff',
    fontSize: 13,
    paddingVertical: 6,
    paddingHorizontal: 10,
    borderRadius: 25,
  },
});
