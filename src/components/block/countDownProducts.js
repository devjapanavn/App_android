import { FlatList, StyleSheet, View } from 'react-native';
import React, { useEffect, useState } from 'react';
import { colors, globalStyles, images, spacing } from '@app/assets';

import { CountdownTime } from '..';
import FastImage from 'react-native-fast-image';
import { ProductItem } from '../ProductItem';
import { Text } from 'react-native-elements';
import _ from 'lodash';
import moment from 'moment';
import { stringHelper } from '@app/utils';

const component = ({ onPress, dataBlock, showStype }) => {
  const [seccondEnd, setSeccondEnd] = useState(0);

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
  const itemContainer = {
    paddingLeft:
      stringHelper.formatToNumber(showStype?.element_padding?.left) ||
      undefined,
    paddingRight:
      stringHelper.formatToNumber(showStype?.element_padding?.right) ||
      undefined,
    paddingBottom:
      stringHelper.formatToNumber(showStype?.element_padding?.bottom) ||
      undefined,
    paddingTop:
      stringHelper.formatToNumber(showStype?.element_padding?.top) || undefined,
  };

  useEffect(() => {
    if (dataBlock && dataBlock.end_date) {
      const endDayMoment = moment(dataBlock.end_date, 'YYYY-MM-DD HH:mm:ss');
      const duration = moment.duration(endDayMoment.diff(moment())).asSeconds();
      setSeccondEnd(parseInt(duration));
    }
    console.log(dataBlock)
  }, [dataBlock?.end_date]);

  const _renderItem = ({ item, index }) => {
    return <ProductItem product={item} containerStyle={itemContainer} />;
  };

  return (
    <View style={[styleContainer]}>
      <View style={styles.container_inside}>
        <View style={styles.header_countdown}>
          {dataBlock &&
            dataBlock['tieu-de'] &&
            dataBlock['tieu-de'].show_type &&
            dataBlock['tieu-de'].show_type !== 'images' ? (
            <Text
              style={[
                globalStyles.text,
                {
                  marginBottom: 10,
                  color: dataBlock['tieu-de']['mau-sac'] || undefined,
                  fontWeight: dataBlock['tieu-de']['font-weight'] || undefined,
                },
              ]}>
              {dataBlock['tieu-de']['name']}
            </Text>
          ) : (
            <FastImage
              source={dataBlock['blocks'] &&
                dataBlock['blocks'].images_mobile ? { uri: dataBlock['blocks'].images_mobile } : images.countdown_image_title}
              style={styles.countdown_img}
              resizeMode="contain"
            />
          )}

          {seccondEnd > 0 ? (
            <CountdownTime
              style={{ marginRight: spacing.tiny }}
              until={seccondEnd}
              // onFinish={() => alert('finished')}
              // onPress={() => alert('hello')}
              size={10}
              timeToShow={['D', 'H', 'M', 'S']}
              timeLabels={{ m: null, s: null, h: null }}
              showSeparator
              digitStyle={{ backgroundColor: showStype?.countdown_background }}
              digitTxtStyle={{ color: showStype?.countdown_text }}
            />
          ) : null}
        </View>
        <FlatList
          keyExtractor={item => `discount_${item.id}`}
          style={styles.listContainer}
          horizontal
          ItemSeparatorComponent={() => (
            <View style={{ width: spacing.medium }} />
          )}
          showsHorizontalScrollIndicator={false}
          data={dataBlock?.products || []}
          renderItem={_renderItem}
        />
      </View>
    </View>
  );
};
export const CountDownProducts = React.memo(component, (prev, next) =>
  _.isEqual(prev.dataBlock, next.dataBlock),
);

const styles = StyleSheet.create({
  container_inside: {
    backgroundColor: '#FFF',
  },
  listContainer: {
    padding: spacing.tiny,
  },
  itemContainer: {
    width: 70,
    justifyContent: 'center',
    alignItems: 'center',
  },
  itemTitle: {
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
  header_countdown: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingBottom: 5,
  },
  countdown_img: { width: 186, height: 38 },
});
