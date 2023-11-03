import { appDimensions, spacing } from '@app/assets';
import _ from 'lodash';
import React from 'react';
import {
  View,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  useWindowDimensions,
} from 'react-native';
import { ImageReponsive } from '../imageReponsive';
import { stringHelper } from '@app/utils';

const Component = ({ banners, indexBlock, onPressLink, showStype }) => {
  const { width } = useWindowDimensions();
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

  const left =
    stringHelper.formatToNumber(showStype?.element_padding?.left) / 2;
  const right =
    stringHelper.formatToNumber(showStype?.element_padding?.right) / 2;
  const top =
    stringHelper.formatToNumber(showStype?.element_padding?.top);
  const bottom =
    stringHelper.formatToNumber(showStype?.element_padding?.bottom);
  return (
    <View style={styleContainer}>
      <FlatList
        key={'block_image_' + indexBlock}
        keyExtractor={(item, index) => 'block_' + indexBlock + '_' + index}
        scrollEnabled={false}
        data={banners}
        renderItem={({ item }) => {
          if (item.images_mobile_1) {
            return (
              <View style={styles.row2Image}>
                <TouchableOpacity
                  style={{ flex: 1, marginRight: right, marginBottom: bottom }}
                  onPress={() => onPressLink(item.link)}
                  activeOpacity={0.8}>
                  <ImageReponsive
                    source={{ uri: item.images_mobile }}
                    containerStyle={[styles.multi_image]}
                  />
                </TouchableOpacity>
                <TouchableOpacity
                  style={{ flex: 1, marginLeft: left, marginBottom: bottom }}
                  onPress={() => onPressLink(item.link2)}
                  activeOpacity={0.8}>
                  <ImageReponsive
                    source={{ uri: item.images_mobile_1 }}
                    containerStyle={[styles.multi_image]}
                  />
                </TouchableOpacity>
              </View>
            );
          } else if (item.images_mobile) {
            return (
              <TouchableOpacity
                style={{ marginBottom: bottom }}
                onPress={() => onPressLink(item.link)}
                activeOpacity={0.8}>
                <ImageReponsive
                  source={{ uri: item.images_mobile }}
                  containerStyle={[styles.one_image, { width: '100%' }]}
                />
              </TouchableOpacity>
            );
          } else {
            return <View />;
          }
        }}
      />
    </View>
  );
};

function areEqual(prevProps, nextProps) {
  return _.isEqual(prevProps.banners, nextProps.banners);
}
export const ImageBlock = React.memo(Component, areEqual);
const styles = StyleSheet.create({
  one_image: {
    height: null,
  },
  multi_image: {
    height: null,
  },
  row2Image: {
    flexDirection: 'row',
  },
});
