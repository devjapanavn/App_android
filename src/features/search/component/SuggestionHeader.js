import {globalStyles, images} from '@app/assets';
import {ProductItem} from '@app/components';
import _ from 'lodash';
import React from 'react';
import {FlatList, Image, StyleSheet, View} from 'react-native';
import {Divider, Text} from 'react-native-elements';

const component = ({suggestion}) => {
  const _renderItem = ({item, index}) => {
    console.log(item);
    return <ProductItem product={item} />;
  };

  return (
    <>
      <View style={{alignItems: 'center', marginVertical: 30}}>
        <Image source={images.ic_no_product} style={{width: 90, height: 90}} />
        <Text
          style={{
            ...globalStyles.text,
            fontSize: 13,
            marginTop: 15,
            color: '#8a8a8f',
          }}>
          Không tìm thấy kết quả phù hợp
        </Text>
      </View>
      {!_.isEmpty(suggestion) ? (
        <View style={styles.sectionContainer}>
          <Text style={styles.titleText}>Gợi ý cho bạn</Text>
          <FlatList
            horizontal
            showsHorizontalScrollIndicator={false}
            key={`suggestion_list_horizontal`}
            keyExtractor={item => `suggestion_list_horizontal${item.id}`}
            data={suggestion || []}
            renderItem={_renderItem}
          />
          <Divider />
        </View>
      ) : null}
      <View style={styles.sectionContainer}>
        <Text style={styles.titleText}>Bán chạy</Text>
      </View>
    </>
  );
};
export const SuggestionHeader = React.memo(component, (prev, next) =>
  _.isEqual(prev.suggestion, next.suggestion),
);

const styles = StyleSheet.create({
  sectionContainer: {
  },
  titleText: {
    ...globalStyles.text,
    fontSize: 16,
    fontWeight: '500',
    marginVertical: 10,
  },
});
