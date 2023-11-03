import {ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import {stringHelper} from '@app/utils';
import _, {wrap} from 'lodash';
import React, {useEffect, useState} from 'react';
import {StyleSheet, TouchableOpacity, View, Dimensions} from 'react-native';
import {Icon, Image, Text, CheckBox} from 'react-native-elements';
const windowDimensions = Dimensions.get('window');

const component = ({data, onPress, isDefault, onEdit}) => {
  const [dimensions, setDimensions] = useState(windowDimensions);
  const [isSelected, setSelection] = useState(false);
  // console.log(data);

  useEffect(()=>{
    if(data.checked){
      setSelection(data.checked)
      // onPress(data.code);
      // console.log(data)
    }
  },[data])

  const changeCheckbox = () => {
    setSelection(!isSelected);
    onPress(data.code);
  };
  const showVoucherDetails = () => {
    navigateRoute(ROUTES.VOUCHER_DETAILS, {changeCheckbox,data}, false, true);
  };
  const checkShowVoucher = () => {
    if (data.is_check_ap_dung == 0) {
      return (
        <View style={[styles.row]}>
          <View style={styles.iconNoti}>
            <Text style={styles.textNoti}> i </Text>
          </View>
          <Text style={[styles.paddingY, styles.textErrNoti]}>
            Sản phẩm không đáp ứng điều kiện áp dụng của voucher
          </Text>
        </View>
      );
    }
  };

  const checkCheckbox = () => {
    if (data.is_check_ap_dung != 0) {
      return (
        <CheckBox
          containerStyle={styles.itemCheckbox}
          checked={isSelected}
          onPress={changeCheckbox}
        />
      );
    }
  };

  return (
    <>
      <View style={[styles.row, styles.boxVoucherItem]}>
        <Image
          source={{uri: data.image_voucher}}
          style={{width: 80, height: 80}}
          resizeMethod="resize"
          resizeMode="contain"
        />
        <View style={[styles.row, styles.voucherItemContent]}>
          <View style={[styles.row]}>
            <Text style={styles.titleVoucherItem}>
              Nhập "{data.code}": {data.details}
            </Text>
            {checkCheckbox()}
          </View>
          <View style={[styles.row, styles.voucherItemContentDetails]}>
            <Text style={[{color: '#cc7025', width: '75%'}]}>
              {data.result_date_voucher.textDate}
            </Text>
            <TouchableOpacity
              onPress={showVoucherDetails}
              style={{width: '20%', alignItems: 'flex-end'}}>
              <Text style={{color: '#2167ff'}}>Điều kiện</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
      {checkShowVoucher()}
    </>
  );
};
export const VoucherItem = React.memo(component);

const styles = StyleSheet.create({
  voucherItemContent: {
    maxWidth: windowDimensions.width - 80,
    flexDirection: 'column',
    justifyContent: 'space-between',
    flexWrap: 'wrap',
    padding: 0,
    paddingLeft: 10,
    paddingRight: 10,
  },
  voucherItemContentDetails: {
    width: windowDimensions.width - 80 - 24,
    justifyContent: 'center',
    padding: 0,
  },
  titleVoucherItem: {
    flex: 1,
  },
  iconNoti: {
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#dc0000',
    width: 20,
    height: 20,
    borderRadius: 100,
  },
  textNoti: {
    color: '#fff',
    fontSize: 14,
  },
  paddingY: {
    paddingTop: 5,
    paddingBottom: 5,
  },
  textErrNoti: {
    color: '#777',
    marginLeft: 5,
  },
  boxVoucherItem: {
    width: '100%',
    alignItems: 'stretch',
    backgroundColor: '#fff',
    elevation: 3,
    borderTopColor: '#eee',
    borderTopWidth: 1,
    borderStyle: 'solid',
  },
  row: {
    alignItems: 'center',
    flexDirection: 'row',
    padding: 5,
  },
  icon: {
    paddingHorizontal: 6,
  },
  text: {
    fontSize: 13,
    lineHeight: 22,
    flex: 1,
  },
  itemCheckbox: {
    backgroundColor: '#fff',
    borderWidth: 0,
    padding: 0,
    margin: 0,
    marginRight: -5,
  },
});
