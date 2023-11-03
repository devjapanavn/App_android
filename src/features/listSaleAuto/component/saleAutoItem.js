import {ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import {stringHelper} from '@app/utils';
import _, {wrap} from 'lodash';
import React, {useState} from 'react';
import {StyleSheet, TouchableOpacity, View, Dimensions} from 'react-native';
import {Icon, Image, Text, CheckBox} from 'react-native-elements';
import FastImage from 'react-native-fast-image';
const windowDimensions = Dimensions.get('window');

const component = ({data, onPress, isDefault, onEdit}) => {
  const [dimensions, setDimensions] = useState(windowDimensions);
  const [changeType, setChangeType] = useState({
    color: '#2367ff',
    text: 'Chọn'
  })
  const changeCheckbox = () => {
    onPress(data)
  };
  return (
    <>
      <TouchableOpacity onPress={changeCheckbox} style={[styles.row, styles.boxSaleAutoitem]}>
        <CheckBox
          checkedIcon="dot-circle-o"
          uncheckedIcon="circle-o"
          containerStyle={styles.itemCheckbox}
          checked={data.check_active == 1 ? true : false}
          onPress={changeCheckbox}
        />
        <FastImage source={{uri: data.image_url}} style={styles.productImage} />
        <View style={styles.productInfo}>
          <Text style={[styles.productInfoTitle, styles.paddingY]}>
            {data.product_name}
          </Text>
          <Text style={[styles.gifText, styles.paddingY]}>Quà tặng </Text>
          <Text style={[styles.productInfoPrice, styles.paddingY]}>
            Số lượng: {data.so_luong}
          </Text>
          <Text
            style={{
              color: '#fff',
              backgroundColor: data.check_active == 1 ? '#17a2b8' : '#2367ff',
              paddingVertical: 2.5,
              width: 70,
              textAlign: 'center',
              borderRadius: 5
            }}>
            { data.check_active == 1 ? 'Đã Chọn' : 'Chọn'}
            
            
          </Text>
        </View>
      </TouchableOpacity>
    </>
  );
};
export const SaleAutoItem = React.memo(component);

const styles = StyleSheet.create({
  boxSaleAutoitem: {
    width: '100%',
    borderColor: '#DDE4EB',
    borderStyle: 'solid',
    borderBottomWidth: 1,
    paddingBottom: 5,
  },
  productInfo: {
    paddingLeft: 5,
    paddingRight: 5,
  },
  productInfoTitle: {
    width: windowDimensions.width - 110,
  },
  row: {
    paddingHorizontal: 0,
    marginVertical: 5,
    flexDirection: 'row',
    alignItems: 'center',
  },
  paddingY: {
    paddingTop: 2.5,
    paddingBottom: 2.5,
  },
  productImage: {
    width: 60,
    height: 60,
    resizeMode: 'contain',
    marginHorizontal: 5,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#d9d9d9',
  },
  itemCheckbox: {
    backgroundColor: '#fff',
    borderWidth: 0,
    padding: 0,
    margin: 0,
  },
});
