import React, {useEffect, useState} from 'react';
import {FlatList, StyleSheet, TouchableOpacity, View} from 'react-native';
import PropTypes from 'prop-types';
import Modal from 'react-native-modal';
import {LocationStorage} from '@app/constants';
import {Header, Text, CheckBox, Divider} from 'react-native-elements';
import {Searchbar} from 'react-native-paper';
import _ from 'lodash';
import {useQuery} from 'react-query';
import api from '@app/api';

const fetch = async (type, id) => {
  switch (type) {
    case 0:
      return await api.getProvince();
    case 1:
      return await api.getDistrict(id);
    case 2:
      return await api.getWard(id);
    default:
      return await api.getProvince();
  }
};

const AddressModalComponent = ({visible, onClose, address, onChange}) => {
  const [currentList, setCurrentList] = useState([]);
  const [selectedAddress, setSelectedAddress] = useState(address);
  const [selected, setSelected] = useState({type: 0, id: 0});
  const [keyword, setKeyword] = useState();

  function onChangeSearch(val) {
    if (keyword && keyword.trim().length > 0) {
      var results = _.filter(data, function (obj) {
        return obj.name.indexOf(val) !== -1;
      });
      setCurrentList(results);
    } else {
      setCurrentList(data);
    }
  }

  const {status, data, error, refetch} = useQuery(
    ['getListAddress', {selected}],
    () => fetch(selected.type, selected.id),
    {
      cacheTime: 0,
      staleTime: 0,
    },
  );

  useEffect(() => {
    onChangeSearch(keyword);
  }, [keyword]);

  useEffect(() => {
    setCurrentList(data);
  }, [data]);

  function onSelectItem(item) {
    switch (selected.type) {
      case 0:
        setSelectedAddress(prev => ({
          ...prev,
          province: item.name,
          province_id: item.id,
          district: '',
          district_id: 0,
          ward: '',
          ward_id: 0,
        }));
        setSelected({type: 1, id: item.id});
        break;
      case 1:
        setSelectedAddress(prev => ({
          ...prev,
          district: item.name,
          district_id: item.id,
          ward: '',
          ward_id: 0,
        }));
        setSelected({type: 2, id: item.id});
        break;
      case 2:
        setSelectedAddress(prev => ({
          ...prev,
          ward: item.name,
          ward_id: item.id,
        }));
        if (onChange) {
          onChange({...selectedAddress, ward: item.name, ward_id: item.id});
        }
        break;
      default:
        setSelectedAddress(prev => ({
          ...prev,
          province: item.name,
          province_id: item.id,
        }));

        break;
    }
  }

  const renderHeader = () => {
    return (
      <View style={{backgroundColor: '#FFF', padding: 10}}>
        <View style={styles.result}>
          <TouchableOpacity onPress={() => setSelected({type: 0, id: 0})}>
            <View style={[styles.resContainer]}>
              <CheckBox
                checkedIcon="radio-button-on-outline"
                uncheckedIcon="ellipse"
                iconType="ionicon"
                checked={selected.type === 0}
                size={18}
                containerStyle={styles.resCheckbox}
              />
              <Text style={styles.resRightTitle}>
                {selectedAddress?.province || 'Chọn Tỉnh/Thành'}
              </Text>
            </View>
          </TouchableOpacity>
          {selectedAddress?.province_id ? (
            <TouchableOpacity
              onPress={() =>
                setSelected({type: 1, id: selectedAddress?.province_id || 0})
              }>
              <View style={[styles.resContainer]}>
                <CheckBox
                  checkedIcon="radio-button-on-outline"
                  uncheckedIcon="ellipse"
                  iconType="ionicon"
                  checked={selected.type === 1}
                  size={18}
                  containerStyle={styles.resCheckbox}
                />
                <Text style={styles.resRightTitle}>
                  {selectedAddress?.district || 'Chọn Quận/Huyện'}
                </Text>
              </View>
            </TouchableOpacity>
          ) : null}
          {selectedAddress?.district_id ? (
            <TouchableOpacity
              onPress={() =>
                setSelected({type: 2, id: selectedAddress?.district_id || 0})
              }>
              <View style={[styles.resContainer]}>
                <CheckBox
                  checkedIcon="radio-button-on-outline"
                  uncheckedIcon="ellipse"
                  iconType="ionicon"
                  checked={selected.type === 2}
                  size={18}
                  containerStyle={styles.resCheckbox}
                />
                <Text style={styles.resRightTitle}>
                  {selectedAddress?.ward || 'Chọn phường/xã'}
                </Text>
              </View>
            </TouchableOpacity>
          ) : null}
        </View>

        <Searchbar
          placeholder="Tìm kiếm "
          onChangeText={setKeyword}
          style={{height: 44}}
          value={keyword}
        />
      </View>
    );
  };

  const renderItem = ({item}) => {
    return (
      <TouchableOpacity onPress={() => onSelectItem(item)}>
        <Text style={styles.item}>{item.name}</Text>
      </TouchableOpacity>
    );
  };

  return (
    <Modal
      style={styles.modalFullsize}
      isVisible={visible}
      onBackButtonPress={onClose}
      onBackdropPress={onClose}>
      <View style={{backgroundColor: '#fff', flex: 1}}>
        <Header
          backgroundColor={'#fff'}
          leftComponent={{
            icon: 'close',
            color: '#888',
            onPress: () => onClose(),
          }}
          centerComponent={{
            text: 'Khu vực nhận hàng',
            style: {color: '#000', fontSize: 16},
          }}
          elevated
        />
        {renderHeader()}
        <FlatList
          removeClippedSubviews={false}
          style={styles.body}
          key={`modal_address_${selected.type}`}
          data={currentList}
          extraData={item => `address_modal_${selected.type}_${item.id}`}
          renderItem={renderItem}
        />
      </View>
    </Modal>
  );
};

AddressModalComponent.propTypes = {
  visible: PropTypes.bool.isRequired,
  onClose: PropTypes.func.isRequired,
  type: PropTypes.string,
};

AddressModalComponent.defaultProps = {
  type: 'province',
};
export const AddressModal = React.memo(
  AddressModalComponent,
  (prev, next) =>
    prev.visible === next.visible && prev.address === next.address,
);

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
    fontSize: 13,
    color: '#000',
  },
});
