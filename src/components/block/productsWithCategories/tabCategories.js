import React, { useState } from 'react';
import { FlatList, StyleSheet, View } from 'react-native';
import _ from 'lodash';
import { Button } from 'react-native-elements';
import { appDimensions, colors, globalStyles, spacing } from '@app/assets';
import { stringHelper } from '@app/utils';

const TabItem = React.memo(
  ({ item, isActive, onChange, colorActive, underlineActive }) => {

    return (
      <Button
        onPress={onChange}
        title={item.name}
        type="clear"
        containerStyle={[styles.tab_item]}
        buttonStyle={isActive ? [styles.tab_item_active, { borderBottomColor: underlineActive }] : null}
        titleStyle={[
          styles.tab_item_title,
          isActive ? [styles.tab_item_title_active, { color: colorActive }] : null,
        ]}
      />
    );
  },
  (prev, next) => prev.isActive === next.isActive,
);
const Component = ({ categories, onChangeTab, rootIndex, showStype }) => {
  const [selectedIndex, setSelectedIndex] = useState(0);
  function onChange(index) {
    setSelectedIndex(index);
    onChangeTab(index);
  }

  const tabStyle = { backgroundColor: showStype?.menu_background };
  return (
    <FlatList
      listKey={(item, index) =>
        `tabcategoryslide_component__key_${rootIndex}_${item.id.toString()}`
      }
      key={'tabCategory'}
      showsHorizontalScrollIndicator={false}
      data={categories}
      extraData={item => `tabproduct_${item.id}`}
      horizontal
      ItemSeparatorComponent={() => (
        <View
          style={{
            width: 1,
            marginVertical: 15,
            backgroundColor: showStype?.menu_background,
          }}
        />
      )}
      renderItem={({ item, index }) => {
        const isActive = index === selectedIndex;
        return (
          <TabItem
            tabStyle={tabStyle}
            underlineActive={showStype?.hover_color || colors.primary}
            colorActive={showStype?.menu_text || colors.primary}
            item={item}
            isActive={isActive}
            onChange={() => onChange(index)}
          />
        );
      }}
    />
  );
};

function areEqual(prev, next) {
  return _.isEqual(prev.categories, next.categories);
}
export default React.memo(Component, areEqual);
const styles = StyleSheet.create({
  tab_item: { padding: 10 },

  tab_item_title: {
    ...globalStyles.text,
    fontSize: 13,
    color: '#555',
  },
  tab_item_active: {
    borderBottomWidth: 1,
    borderRadius: 0,
  },
  tab_item_title_active: {
    fontSize: 13,
    color: colors.primary,
    fontWeight: '700'
  },
});
