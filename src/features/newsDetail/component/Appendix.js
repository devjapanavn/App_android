import {globalStyles} from '@app/assets';
import React from 'react';
import {FlatList, StyleSheet} from 'react-native';
import {View} from 'react-native';
import {Button, Text} from 'react-native-elements';
const datas = [
  {id: 1, name: '1. Tác hại từ ánh nắng mặt trời', isSub: false},
  {id: 2, name: '1.1 Tia UVA là gì?', isSub: true},
  {id: 3, name: '1.2 Tia UVB là gì?', isSub: true},
  {
    id: 4,
    name: '2. Những điểm khác biệt giữa kem chống nắng vật lý và hóa học',
    isSub: false,
  },
  {id: 5, name: '2.1 Kem chống nắng vật lý', isSub: true},
  {id: 6, name: '2.2 Kem chống nắng hóa học', isSub: true},
  {id: 7, name: '2.3 Các sản phẩm nổi bật', isSub: true},
  {id: 8, name: '2.4 Lựa chọn phù hợp', isSub: true},
  {id: 9, name: '2.5 Các tips khi sử dụng kem chống nắng', isSub: true},
];
const Component = () => {
  const renderItem = ({item, index}) => {
    return (
      <View>
        <Text style={[styles.title, item.isSub ? styles.isSub : null]}>
          {item.name}
        </Text>
      </View>
    );
  };
  return (
    <FlatList
      contentContainerStyle={styles.container}
      scrollEnabled={false}
      ListFooterComponent={() => (
        <Button
          type="clear"
          title={'Xem thêm'}
          titleStyle={{color: '#3b4859', fontSize: 13}}
          iconRight
          icon={{
            name: 'chevron-down-outline',
            type: 'ionicon',
            color: '#3b4859',
            size:13
          }}
        />
      )}
      data={datas}
      keyExtractor={item => 'newsAppendix_' + item.id}
      renderItem={renderItem}
    />
  );
};
export const Appendix = React.memo(Component, (prev, next) => false);
const styles = StyleSheet.create({
  container: {
    backgroundColor: '#f0f8ff',
    borderRadius: 4,
    borderWidth: 1,
    borderColor: '#bdd2ff',
    padding: 10,
    marginVertical: 10,
  },
  title: {
    ...globalStyles.text,
    fontSize: 14,
    color: '#0c4fdf',
    marginVertical: 4,
  },
  isSub: {
    marginLeft: 8,
  },
});
