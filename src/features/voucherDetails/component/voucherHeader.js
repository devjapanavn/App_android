import {ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import {stringHelper} from '@app/utils';
import _, {wrap} from 'lodash';
import React, {useState} from 'react';
import {StyleSheet, TouchableOpacity, View, Dimensions} from 'react-native';
import {Icon, Image, Text, CheckBox} from 'react-native-elements';
const windowDimensions = Dimensions.get('window');

const component = ({data, onPress, isDefault, onEdit}) => {
  return (
    <View style={[styles.row, styles.boxVoucherItem]}>
      <Image
        source={{uri: data.image_voucher}}
        style={{width: 80, height: 80}}
        resizeMethod="resize"
        resizeMode="contain"
      />
      <View style={[styles.row, styles.voucherItemContent]}>
        <Text style={{width: '100%'}}>
          Nhập {data.code}: {data.promotion_name}
        </Text>
        <View style={[styles.row, styles.voucherItemContentDetails]}>
          <Text style={{color: '#cc7025', width: '75%'}}>
            {data.result_date_voucher.textDate}
          </Text>
        </View>
      </View>
    </View>
  );
};
export const VoucherHeader = React.memo(component);

const styles = StyleSheet.create({
  voucherItemContent: {
    maxWidth: windowDimensions.width - 80,
    flexDirection: 'column',
    justifyContent: 'space-between',
    flexWrap: 'wrap',
    padding: 0,
    paddingLeft: 10,
  },
  voucherItemContentDetails: {
    padding: 0
  },
  boxVoucherItem: {
    width: '100%',
    alignItems: 'stretch',
    marginTop: -35,
    elevation: 5,
    backgroundColor: '#fff',
  },
  row: {
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
  },
});
