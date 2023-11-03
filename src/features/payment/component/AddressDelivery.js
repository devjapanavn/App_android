import React, { useEffect, useState } from 'react';
import { StyleSheet, View } from 'react-native';
import { Button, Icon, Text } from 'react-native-elements';
import { colors, globalStyles } from '@app/assets';
import _ from 'lodash';
import { stringHelper } from '@app/utils';
import { TouchableNativeFeedbackBase } from 'react-native';
import { navigateRoute } from '@app/route';
import { ROUTES } from '@app/constants';
import api from '@app/api';
import { useSelector } from 'react-redux';

export const AddressDelivery = React.memo(
  ({ data, onUpdatePayment }) => {
    const [address, setAddress] = useState(data);
    const { user } = useSelector(state => ({
      user: state.auth.user,
    }));

    useEffect(() => {
      setAddress(data);
    }, [data]);

    function changeAddress() {
      navigateRoute(ROUTES.ADDRESS_LIST, { onSelect }, false, true);
    }

    async function onSelect(location) {
      setAddress(location)
      if (onUpdatePayment) {
        const res = await api.updateCheckOutTemp({
          member_id: user?.id,
          id_member_address: location.id,
        });
        onUpdatePayment()
      }
    }

    if (_.isEmpty(address)) {
      return (
        <View style={{ justifyContent: 'center' }}>
          <Text
            style={{
              textAlign: 'center',
              ...globalStyles.text,
              color: colors.primary,
            }}>
            Địa chỉ nhận hàng trống. Vui lòng thêm địa chỉ nhận hàng
          </Text>
          <Button
            title={'Thêm địa chỉ nhận hàng '}
            type="solid"
            onPress={changeAddress}
            buttonStyle={{ backgroundColor: '#2266ff', marginVertical: 10 }}
          />
        </View>
      );
    }
    return (
      <>
        <View style={styles.itemContainer}>
          <Icon type="ionicon" name="person" color="#bdbdbd" size={22} />
          <Text style={styles.text}>
            {address?.fullname || '--'}{' '}
            {`${address?.mobile ? '-- ' + address?.mobile : null}`}
          </Text>
          <Icon
            name="edit"
            color={colors.link}
            onPress={changeAddress}
            Component={TouchableNativeFeedbackBase}
          />
        </View>
        <View style={styles.itemContainer}>
          <Icon type="ionicon" name="paper-plane" color="#bdbdbd" size={22} />
          <Text style={styles.text}>
            {address
              ? stringHelper.generateFullAddress(
                address?.address,
                address?.ward,
                address?.district,
                address?.province,
              )
              : '--'}
          </Text>
        </View>
      </>
    );
  },
  (prev, next) => _.isEqual(prev.data, next.data),
);

const styles = StyleSheet.create({
  itemContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 10,
  },
  text: {
    ...globalStyles.text,
    paddingLeft: 8,
    flex: 1,
  },
});
