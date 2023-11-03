import {appDimensions, globalStyles} from '@app/assets';
import {ProductSlide} from '@app/components';
import React from 'react';
import {StyleSheet} from 'react-native';
import {View} from 'react-native';
import {Text} from 'react-native-elements';
const SuggestionProductsComponent = () => {
  return (
    <View style={styles.container}>
      <View style={styles.headerContainer}>
        <Text style={styles.title}>Có thể bạn quan tâm</Text>
      </View>
      <ProductSlide totalPage={3} />
    </View>
  );
};
export const SuggestionProducts = React.memo(SuggestionProductsComponent, (prev, next) => true);
const styles = StyleSheet.create({
  separator: {
    width: 0.3,
    backgroundColor: '#fff',
  },
  container: {
    backgroundColor: '#fff',
    paddingHorizontal: 10,
    marginBottom: 10,
  },
  title: {
    ...globalStyles.text,
    fontSize: 16,
    fontWeight: '500',
    lineHeight: 24,
    paddingVertical: 4,
  },
  headerContainer: {
    borderBottomColor: '#ffa200',
    borderBottomWidth: 2,
  },
});
