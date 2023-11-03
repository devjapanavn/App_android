import {globalStyles, spacing} from '@app/assets';
import {BLOCK_ENUM} from '@app/constants';
import {stringHelper} from '@app/utils';
import _ from 'lodash';
import React, {useState} from 'react';
import {FlatList, StyleSheet, View} from 'react-native';
import {Chip} from 'react-native-elements';

const component = ({menu, showStype, blockIndex}) => {
  console.log('showStype', showStype);
  const [selectedIndex, setIndex] = useState(0);
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
  const styleElement = {
    backgroundColor: showStype?.menu_background || undefined,
    color: showStype?.text_color || undefined,
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
  return (
    <View style={[styles.container, styleContainer]}>
      <FlatList
        data={menu}
        keyExtractor={item =>
          BLOCK_ENUM.BLOCK_MENU + '_' + blockIndex + '_' + item.sort
        }
        horizontal
        showsHorizontalScrollIndicator={false}
        ItemSeparatorComponent={() => <View style={styles.separator} />}
        renderItem={({item, index}) => {
          const isActive = index === selectedIndex;
          return (
            <Chip
              onPress={() => setIndex(index)}
              title={item.text}
              titleStyle={[styles.itemTitle, {color: showStype?.text_color}]}
              type="solid"
              buttonStyle={styleElement}
            />
          );
        }}
      />
    </View>
  );
};
export const HeaderSubMenu = React.memo(
  component,
  (prev, next) =>
    _.isEqual(prev.menu, next.menu) &&
    _.isEqual(prev.showStype, next.showStype),
);

const styles = StyleSheet.create({
  container: {},
  itemTitle: {
    ...globalStyles.text,
    fontSize: 12,
  },
  separator: {
    width: spacing.medium,
  },
});
