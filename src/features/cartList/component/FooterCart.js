import api from '@app/api';
import { globalStyles } from '@app/assets';
import { ROUTES } from '@app/constants';
import { navigateRoute } from '@app/route';
import { stringHelper } from '@app/utils';
import _ from 'lodash';
import React, { useCallback, useState } from 'react';
import { StyleSheet, View } from 'react-native';
import { Button, Divider, Text } from 'react-native-elements';
import { useDispatch, useSelector } from 'react-redux';
export const FooterCart = React.memo(
  () => {
    const { user, totalCart, totalCartPrice, text_promotion_cart } = useSelector(state => ({
      user: state.auth.user,
      totalCart: state.auth.totalCart,
      totalCartPrice: state.auth.totalCartPrice,
      text_promotion_cart: state.auth.text_promotion_cart
    }));
    const [checking, setChecking] = useState(false);

    const dispatch = useDispatch();

    const onsubmit = useCallback(async () => {
      setChecking(true);
      try {
        await api.addCheckOutTemp(user?.id);
        setChecking(false);
        setTimeout(() => {
          navigateRoute(ROUTES.PAYMENT);
        }, 300);
      } catch (error) {
        setChecking(false);
      }
    }, [user]);

    return (
      <View style={{ backgroundColor: '#fff' }}>
        {text_promotion_cart && text_promotion_cart.trim() !== '' ?
          <>
            <Divider />
            <Text style={styles.promoteText}>{text_promotion_cart}</Text>
          </>
          : null}
        <Divider />
        {/* <Text style={styles.footerDiscount}>
            Giá trị đơn hàng hiện tại nhỏ hơn 1.500.000đ. Để được miễn phí giao
            hàng, Quý khách vui lòng chọn thêm sản phẩm.
          </Text> */}
        {/* <Divider /> */}
        <View
          style={{
            flexDirection: 'row',
            justifyContent: 'space-between',
            alignItems: 'center',
            padding: 10,
          }}>
          <View>
            <Text style={{ fontSize: 13, color: '#000', textAlign: 'left' }}>
              Tạm tính :{' '}
              <Text style={{ color: '#dc0000' }}>
                {stringHelper.formatMoney(totalCartPrice)} đ
              </Text>
            </Text>
            <Text style={{ fontSize: 12, color: '#555' }}>
              ({totalCart} sản phẩm)
            </Text>
          </View>
          <Button
            loading={checking}
            title="ĐẶT HÀNG"
            titleStyle={{ fontSize: 17, color: '#fff' }}
            buttonStyle={styles.buttonSubmit}
            onPress={onsubmit}
          />
        </View>
      </View>

    );
  },
  (prev, next) => true,
);
const styles = StyleSheet.create({
  buttonSubmit: {
    width: 150,
    height: 48,
    borderRadius: 4,
    backgroundColor: '#dc0000',
  },
  promoteText: {
    ...globalStyles.text,
    color: '#0f83ff',
    fontSize: 13,
    padding: 10
  }
});
