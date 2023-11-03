import {
  convertDateStringToString,
  formatStringDate,
  stringHelper,
  toastAlert,
} from '@app/utils';
import Clipboard from '@react-native-clipboard/clipboard';
import React from 'react';
import {FlatList, StyleSheet, View} from 'react-native';
import {Text, Button} from 'react-native-elements';

const Component = ({coupon}) => {
  async function coppy(val) {
    await Clipboard.setString(val);
    toastAlert('Đã sao chép mã khuyến mãi: ' + val);
  }
  console.log(coupon)
 
  return (
    <View style={styles.box}>
      <Text style={styles.headerTitleStyle}>Mã giảm giá</Text>
      <View style={styles.itemContainer}>
        <View style={styles.itemHeadBox}>
          <Text style={styles.itemHeadTitle}>GIẢM NGAY</Text>
          <Text style={styles.itemHeadValue}>
            {coupon.price_km && stringHelper.formatToNumber(coupon.price_km) > 0
              ? `${stringHelper.formatMoney(coupon.price_km)} đ`: ''
            }
          </Text>
        </View>
        <View style={styles.itemCenterBox}>
          <Text style={styles.itemCenterCode}>{coupon.code}</Text>
          <Text style={styles.itemCenterExpire}>
            (Hết hạn:{' '}
            {coupon.result_date_voucher.endDate})
          </Text>
        </View>
        <Button
          onPress={() => coppy(coupon.code)}
          title={'Sao chép'}
          containerStyle={styles.itemButtonCoppy}
          buttonStyle={styles.itemButtonCoppyStyle}
          titleStyle={styles.itemButtonCoppyText}
        />
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  box: {
    marginVertical: 4,
    marginBottom: 4,
    padding: 10,
    backgroundColor: '#fff',
  },
  headerTitleStyle: {
    fontSize: 16,
    color: '#000',
    fontWeight: '500',
    flex: 1,
    marginBottom: 8,
  },
  itemContainer: {
    marginVertical: 4,
    borderRadius: 4,
    borderWidth: 1,
    borderColor: '#e3e3e3',
    flexDirection: 'row',
    alignItems: 'center',
  },
  itemHeadBox: {
    backgroundColor: 'rgba(220, 0, 0, 0.1)',
    height: 46,
    width: 101,
    justifyContent: 'center',
    alignItems: 'center',
  },
  itemHeadTitle: {
    color: '#000',
    fontSize: 13,
    textAlign: 'center',
  },
  itemHeadValue: {
    color: 'red',
    fontSize: 13,
    textAlign: 'center',
  },
  itemCenterBox: {
    paddingLeft: 15,
    flex: 1,
  },
  itemCenterCode: {
    color: '#000000',
    fontSize: 13,
  },
  itemCenterExpire: {
    color: '#888',
    fontSize: 13,
  },
  itemButtonCoppy: {
    marginRight: 10,
  },
  itemButtonCoppyStyle: {
    padding: 6,
  },
  itemButtonCoppyText: {
    fontSize: 12,
    color: '#fff',
  },
});

function areEqual(prev, next) {
  return prev.coupon === next.coupon;
}
export const ProductDiscountCode = React.memo(Component, areEqual);
