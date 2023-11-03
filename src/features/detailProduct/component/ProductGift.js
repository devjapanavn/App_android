import React from 'react';
import {SectionList, StyleSheet, View} from 'react-native';
import {Text} from 'react-native-elements';
import {stringHelper} from '@app/utils';
import {ImageReponsive} from '@app/components';
import _ from 'lodash';
const Component = ({gift}) => {
  console.log(gift)
  return (
    <>
      <View style={[styles.box_gift, styles.box]}>
        <View>
          <ImageReponsive
            source={{
              uri: gift.image_url,
            }}
            containerStyle={styles.img_gift}
          />
        </View>
        <View style={styles.desc_gift}>
          <Text style={styles.text_gift}>
            <Text style={styles.title_gift}>Tặng kèm:</Text> {gift.product_name}
          </Text>
          <Text style={styles.text_gift}>
            {stringHelper.formatMoney(gift.price)} đ
          </Text>
        </View>
      </View>
    </>
  );
};

const styles = StyleSheet.create({
  box: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingBottom: 4,
  },
  box_gift: {
    flexDirection: 'row',
    borderStyle: 'dashed',
    borderWidth: 1,
    borderColor: '#ff9524',
    borderRadius: 4,
    padding: 8,
    backgroundColor: '#fff7d6',
    marginTop: 5,
    marginBottom: 5,
  },
  desc_gift: {flex: 1, marginLeft: 4},
  img_gift: {width: 40, height: 40},
  title_gift: {
    color: '#0F83FF',
  },
  text_gift: {
    color: '#2a2a2a',
    fontSize: 12,
    lineHeight: 18,
    marginBottom: 4,
  },
  discount_note: {
    color: '#0F83FF',
    fontSize: 13,
  },
});

function areEqual(prev, next) {
  return _.isEqual(prev.gift, next.gift);
}
export const ProductGift = React.memo(Component, areEqual);
