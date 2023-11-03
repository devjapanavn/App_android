import {appDimensions, images} from '@app/assets';
import React from 'react';
import {FlatList, StyleSheet, View, Image} from 'react-native';
import {Text} from 'react-native-elements';

const data = [
  {
    img: images.benefit_item_02,
    title: '100% sản phẩm được kiểm soát chất lượng',
  },
  {img: images.benefit_item_01, title: 'Cam kết 90 ngày đổi trả miễn phí'},
];
const Component = () => {
  const _renderItem = ({item, index}) => {
    return (
      <View style={styles.itemContainer}>
        <Image
          source={item.img}
          style={{width: 26, height: 26, resizeMode: 'contain'}}
        />
        <Text style={styles.itemTitle}>{item.title}</Text>
      </View>
    );
  };
  return (
    <View style={styles.box}>
      <FlatList
        numColumns={2}
        data={data}
        columnWrapperStyle={{justifyContent: 'space-between'}}
        renderItem={_renderItem}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  box: {
    marginVertical: 4,
    padding: 10,
    backgroundColor:'#fff'
  },
  itemContainer: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    marginHorizontal: 10,
  },
  itemTitle: {
    flex: 1,
    flexWrap: 'wrap',
    fontSize: 12,
    color: '#000',
    marginLeft: 8,
  },
});

function areEqual(prev, next) {
  return true;
}
export const Benefit = React.memo(Component, areEqual);
