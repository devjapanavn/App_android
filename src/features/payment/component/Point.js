import api from '@app/api';
import { colors, globalStyles } from '@app/assets';
import { stringHelper } from '@app/utils';
import React, { useEffect, useState } from 'react';
import { StyleSheet, View, Image, TouchableOpacity } from 'react-native';
import { CheckBox, Text } from 'react-native-elements';
import Spinner from 'react-native-spinkit';
import { useSelector } from 'react-redux';

export const Point = React.memo(
  ({ points, userId, pointPayment, onUpdatePayment }) => {
    const {voucher_code} = useSelector(state => ({
      voucher_code: state.checkout.voucher_code,
    }))
    const [pointSelected, setPoinSelected] = useState(pointPayment === '1');
    const [isLoading, setIsLoading] = useState(false);
    async function onToggle() {
      setIsLoading(true);
      try {
        const res = await api.updateCheckOutTemp({
          member_id: userId,
          point_payment: pointSelected ? '0' : '1',
          voucher_code
        });
        if (onUpdatePayment) {
          onUpdatePayment()
        }
        setTimeout(() => {
          setIsLoading(false);
          setPoinSelected(prev => !prev);
        }, 500);
      } catch (error) {
        console.log('error', error);
        setIsLoading(false);
      }
    }

    return (
      <View>
        <Text style={styles.title}>
          Bạn đang có {points?.point || 0} điểm tích lũy (tương ứng{' '}
          {stringHelper.formatMoney(points?.point_to_money || 0)} đ)
        </Text>
        <TouchableOpacity style={styles.itemContainer} onPress={onToggle}>
          {isLoading ? (
            <Spinner
              type="Circle"
              color={colors.link}
              size={18}
              style={{
                padding: 0,
                marginVertical: 4,
                marginHorizontal: 10,
              }}
            />
          ) : (
            <CheckBox
              checked={pointSelected}
              containerStyle={styles.itemCheckbox}
              onPress={onToggle}
            />
          )}
          <Text style={styles.itemRightTitle}>
            Sử dụng điểm hiện có để thanh toán
          </Text>
        </TouchableOpacity>
      </View>
    );
  },
  (prev, next) => prev.points === next.points && prev.userId === next.userId && prev.pointPayment === next.pointPayment,
);

const styles = StyleSheet.create({
  itemContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 10,
  },
  title: {
    ...globalStyles.text,
    color: colors.link,
    fontSize: 12,
    marginVertical: 8,
  },
  itemCheckbox: {
    backgroundColor: '#fff',
    borderWidth: 0,
    padding: 0,
    margin: 0,
  },
});
