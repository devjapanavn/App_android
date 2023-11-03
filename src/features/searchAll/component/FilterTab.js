import {colors, spacing} from '@app/assets';
import React, {useCallback, useEffect, useState} from 'react';
import {StyleSheet, View} from 'react-native';
import {ButtonGroup, Icon} from 'react-native-elements';

const component = ({openFilter, onChangeFilterType}) => {
  const [selectedSort, setSelectedSort] = useState(0);
  const [modalFilterVisible, setModalFilterVisible] = useState(false);

  useEffect(() => {
    onChangeFilterType(selectedSort);
  }, [selectedSort]);

  const handleOpenFilter = useCallback(() => {
    if (openFilter) {
      openFilter();
    }
  }, []);

  return (
    <View
      style={{
        flexDirection: 'row',
        justifyContent: 'space-between',
        backgroundColor: '#fff',
        elevation: 2,
        alignItems: 'center',
      }}>
      <ButtonGroup
        onPress={setSelectedSort}
        selectedIndex={selectedSort}
        buttons={['Mặc định', 'Sale', 'Giá cao', 'Giá thấp']}
        containerStyle={{
          flex: 1,
          padding: 0,
          marginHorizontal: 0,
          marginVertical: 0,
          borderColor: 'transparent',
        }}
        buttonContainerStyle={{borderColor: 'transparent'}}
        selectedButtonStyle={{backgroundColor: '#fff'}}
        selectedTextStyle={{color: '#2367ff'}}
      />
      <Icon
        containerStyle={{
          flex: 0,
          marginVertical: 5,
          borderLeftWidth: 0.5,
          borderLeftColor: '#888',
          paddingHorizontal: 15,
          paddingVertical: 10,
        }}
        onPress={handleOpenFilter}
        name="filter-list-alt"
        type="material"
        size={25}
        color="rgb(59, 72, 89)"
      />
    </View>
  );
};
export const FilterTab = React.memo(component, () => true);

const styles = StyleSheet.create({});
