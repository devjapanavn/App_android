import React, {useCallback, useEffect, useState} from 'react';
import {
  FlatList,
  InteractionManager,
  SectionList,
  StyleSheet,
  TouchableOpacity,
  View,
} from 'react-native';
import PropTypes from 'prop-types';
import Modal from 'react-native-modal';
import {
  Button,
  CheckBox,
  Divider,
  Header,
  Icon,
  ListItem,
  Text,
} from 'react-native-elements';
import _ from 'lodash';
import {colors} from '@app/assets';
import {stringHelper} from '@app/utils';
import {RangeSlider} from '@app/components';
const sections = [
  {
    name: 'Danh mục',
    data: [
      {name: 'Collagen', id: 1, checked: false},
      {name: 'Thực phẩm làm đẹp', id: 2, checked: true},
      {name: 'Giảm cân', id: 3, checked: false},
      {name: 'Chăm sóc sức khỏe', id: 4, checked: false},
      {name: 'Trang điểm', id: 5, checked: true},
    ],
  },
  {
    name: 'Thương hiệu',
    data: [
      {name: 'DHC', id: 6, checked: false},
      {name: 'SK-II', id: 7, checked: true},
      {name: 'SK-III', id: 8, checked: false},
      {name: 'SK-IV', id: 9, checked: false},
      {name: 'SK-V', id: 10, checked: false},
    ],
  },
];

const ModalFilterComponent = ({visible, onClose, type}) => {
  const [low, setLow] = useState(0);
  const [high, setHigh] = useState(100);
  const [onReady, setOnReady] = useState(false);

  const handleValueChange = useCallback((low, high) => {
    setLow(low);
    setHigh(high);
  }, []);

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      setOnReady(false);
      interactionPromise.cancel();
    };
  }, []);

  const renderItem = ({item}) => {
    return (
      <ListItem bottomDivider>
        <ListItem.Content>
          <ListItem.Subtitle style={{fontSize: 15}}>
            {item.name}
          </ListItem.Subtitle>
        </ListItem.Content>
        <ListItem.CheckBox
          checked={item.checked}
          uncheckedColor={'transparent'}
          checkedIcon={
            <Icon
              name="checkmark-sharp"
              type="ionicon"
              color="rgb(0, 153, 255)"
            />
          }
        />
      </ListItem>
    );
  };

  const renderSectionHeader = ({section}) => {
    return (
      <View
        style={{
          flexDirection: 'row',
          paddingHorizontal: 10,
          alignItems: 'center',
          justifyContent: 'space-between',
          paddingVertical: 10,
        }}>
        <Text style={{fontSize: 17, fontWeight: '500'}}>{section.name}</Text>
        <CheckBox
          title="Chọn tất cả"
          titleProps={{
            style: {fontSize: 13, fontWeight: 'normal', color: '#3b4859'},
          }}
          checked
          containerStyle={{
            alignItems: 'center',
            padding: 0,
            margin: 0,
            backgroundColor: '#fff',
            borderColor: '#fff',
          }}
        />
      </View>
    );
  };

  const renderHeader = () => {
    return (
      <View style={{padding: 10}}>
        <Text style={{fontSize: 17, marginBottom: 10, fontWeight: '500'}}>
          Giá
        </Text>
        <View style={{flexDirection: 'row', justifyContent: 'space-between'}}>
          <Text style={{fontSize: 16}}>{stringHelper.formatMoney(low)} đ</Text>
          <Text style={{fontSize: 16}}>{stringHelper.formatMoney(high)} đ</Text>
        </View>
        <RangeSlider />
      </View>
    );
  };

  return (
    <Modal
      style={styles.modalFullsize}
      isVisible={visible}
      onBackButtonPress={onClose}
      onBackdropPress={onClose}>
      <View style={{backgroundColor: '#fff', flex: 1}}>
        {visible && onReady ? (
          <>
            <Header
              backgroundColor={colors.primary}
              rightComponent={{
                icon: 'md-close-circle-outline',
                type: 'ionicon',
                color: '#fff',
                size: 30,
                onPress: () => onClose(),
              }}
              leftComponent={{
                text: 'Bộ lọc',
                style: {color: '#fff', fontSize: 24, fontWeight: '500'},
              }}
              elevated
            />
            {renderHeader()}
            <SectionList
              sections={sections}
              renderItem={renderItem}
              renderSectionHeader={renderSectionHeader}
            />
            <View style={styles.footerContainer}>
              <Divider />
              <Button
                title={'Xóa'}
                type="outline"
                containerStyle={styles.footerButtonContainer}
                buttonStyle={{borderColor: '#3b4859'}}
                titleStyle={{color: '#3b4859'}}
              />
              <Button
                title={'Áp dụng'}
                type="solid"
                containerStyle={styles.footerButtonContainer}
                buttonStyle={{backgroundColor: '#dc0000'}}
              />
            </View>
          </>
        ) : null}
      </View>
    </Modal>
  );
};

ModalFilterComponent.propTypes = {
  visible: PropTypes.bool.isRequired,
  onClose: PropTypes.func.isRequired,
  type: PropTypes.string,
};

ModalFilterComponent.defaultProps = {
  type: 'province',
};
export const ModalFilter = React.memo(
  ModalFilterComponent,
  (prev, next) => prev.visible === next.visible,
);

const THUMB_RADIUS = 8;
const styles = StyleSheet.create({
  modalFullsize: {
    margin: 0,
    padding: 0,
  },

  headerContainer: {
    flexDirection: '',
  },
  body: {},
  item: {
    color: '#2a2a2a',
    fontSize: 14,
    padding: 10,
  },
  resContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 10,
  },
  selectedRes: {
    backgroundColor: '#e5f2ff',
    borderRadius: 4,
  },
  resCheckbox: {
    padding: 0,
    margin: 0,
  },
  resRightTitle: {
    fontSize: 14,
    color: '#000',
  },
  thumb: {
    width: THUMB_RADIUS * 2,
    height: THUMB_RADIUS * 2,
    borderRadius: THUMB_RADIUS,
    borderWidth: 2,
    borderColor: '#7f7f7f',
    backgroundColor: '#ffffff',
  },
  rail: {
    flex: 1,
    height: 3,
    borderRadius: 2,
    backgroundColor: '#c8c7cc',
  },
  railSelected: {
    height: 3,
    backgroundColor: '#dc0000',
    borderRadius: 2,
  },
  footerContainer: {
    flexDirection: 'row',
  },
  footerButtonContainer: {
    flex: 1,
    margin: 10,
  },
});
