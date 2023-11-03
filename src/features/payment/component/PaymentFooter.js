import api from '@app/api';
import { ModalWebView } from '@app/components';
import { ROUTES } from '@app/constants';
import { resetAndNavigateRoute } from '@app/route';
import { stringHelper } from '@app/utils';
import React, { useCallback, useState } from 'react';
import { StyleSheet, View } from 'react-native';
import { Button, Divider, Text } from 'react-native-elements';
import WebView from 'react-native-webview';
import { useSelector } from 'react-redux';

export const PaymentFooter = React.memo(
  ({ totalItem, totalPrice, userId, products }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [modal, setModal] = useState({ url: null, visible: false });
    let {gift_order_auto,voucher_code} = useSelector(state => ({
      gift_order_auto: state.checkout.gift_order_auto,
      voucher_code: state.checkout.voucher_code,
    }))
   
    const submit = useCallback(async () => {
      setIsLoading(true);
      try {
        const res = await api.addCheckOut({member_id: userId, gift_order_auto,voucher_code,nguonkh: 26});
        if (res && res.url) {
          setModal({ url: res.url, visible: true })
        } else {
          resetAndNavigateRoute([
            { name: ROUTES.MAIN_TABS },
            { name: ROUTES.PAYMENT_SUCCESS },
          ]);
        }
        setIsLoading(false);
      } catch (error) {
        setIsLoading(false);
      }
    }, []);
    function onCloseModal() {
      setModal({ url: null, visible: false })
      setTimeout(() => {
        resetAndNavigateRoute([
          { name: ROUTES.MAIN_TABS },
          { name: ROUTES.PAYMENT_SUCCESS },
        ]);
      }, 300);

    }
    return (
      <>
        <Divider />
        <View style={styles.container}>
          <View>
            <Text style={{ fontSize: 13, color: '#000', textAlign: 'left' }}>
              Tổng cộng :
              <Text style={{ color: '#dc0000' }}>
                {stringHelper.formatMoney(totalPrice)} đ
              </Text>
            </Text>
            <Text style={{ fontSize: 12, color: '#555' }}>
              ({totalItem} sản phẩm)
            </Text>
          </View>
          <Button
            loading={isLoading}
            title="Thanh toán"
            titleStyle={{ fontSize: 17, color: '#fff' }}
            buttonStyle={styles.buttonSubmit}
            onPress={submit}
          />
        </View>
        <ModalWebView visible={modal.visible} url={modal.url} onClose={onCloseModal} />
      </>
    );
  },
  (prev, next) =>
    prev.totalItem === next.totalItem &&
    prev.totalPrice === next.totalPrice &&
    prev.userId === next.userId,
);

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 10,
    backgroundColor: '#fff',
  },
  buttonSubmit: {
    width: 150,
    height: 48,
    borderRadius: 4,
    backgroundColor: '#dc0000',
  },
});
