import {globalStyles, images} from '@app/assets';
import {ProductItem} from '@app/components';
import {ROUTES} from '@app/constants';
import {resetRoute} from '@app/route';
import _ from 'lodash';
import React from 'react';
import {FlatList, StyleSheet, View} from 'react-native';
import {Button, Divider, Text} from 'react-native-elements';
import FastImage from 'react-native-fast-image';
import {iOSColors} from 'react-native-typography';

export const EmptyCart = React.memo(
  ({suggestionList}) => {
    return (
      <View>
        <View style={styles.header}>
          <FastImage source={images.empty_cart} style={styles.empCartImg} />
          <Text style={styles.title}>
            Không có sản phẩm nào trong giỏ hàng của bạn!
          </Text>
          <Button
            title={'Tiếp tục mua sắm'}
            buttonStyle={styles.buttonStyle}
            onPress={() => resetRoute(ROUTES.MAIN_TABS)}
          />
        </View>
        <Divider />
        <View style={globalStyles.m_10}>
          <Text style={styles.suggestionTitle}>Gợi ý cho bạn</Text>
          <Text style={styles.suggestionSubTitle}>
            ({suggestionList.length} sản phầm)
          </Text>
        </View>
        <FlatList
          contentContainerStyle={globalStyles.m_10}
          horizontal
          showsHorizontalScrollIndicator={false}
          key={`emptycart_list_horizontal`}
          keyExtractor={item => `emptycart_list_horizontal${item.id}`}
          data={suggestionList || []}
          renderItem={({item}) => <ProductItem product={item} />}
        />
      </View>
    );
  },
  (prev, next) => _.isEqual(prev.suggestionList, next.suggestionList),
);
const styles = StyleSheet.create({
  header: {
    ...globalStyles.m_10,
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 20,
  },
  title: {
    ...globalStyles.text,
    color: '#ccc',
    marginVertical: 5,
  },
  empCartImg: {
    height: 120,
    width: 120,
    resizeMode: 'contain',
  },
  buttonStyle: {
    backgroundColor: iOSColors.red,
    paddingHorizontal: 20,
    marginVertical: 10,
  },
  suggestionTitle: {
    ...globalStyles.text,
    fontSize: 18,
    fontWeight: '500',
    marginTop: 10,
  },
  suggestionSubTitle: {
    ...globalStyles.text,
    color: '#ccc',
    marginVertical: 5,
  },
});
