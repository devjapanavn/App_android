import React, {useEffect, useState} from 'react';
import {StyleSheet, View, InteractionManager, ScrollView} from 'react-native';
import {useForm} from 'react-hook-form';
import {Divider} from 'react-native-elements';
import {Text} from 'react-native-animatable';
import {
  AddressDelivery,
  Discount,
  PaymentFooter,
  PaymentMethod,
  PaymentProducts,
  Point,
} from './component';
import {AddressModal} from '@app/components';
import api from '@app/api';
import {useQuery} from 'react-query';
import {useSelector} from 'react-redux';
import {colors, spacing} from '@app/assets';
import Spinner from 'react-native-spinkit';
import {updatelistVoucher} from '@app/store/checkout/services';
import {useDispatch} from 'react-redux';

const fetch = async userid => {
  return api.getCheckOutTemp(userid);
};

const Screen = props => {
  const dispatch = useDispatch();
  const [onReady, setOnReady] = useState(false);
  const [modalVisible, setModalVisible] = useState(false);
  const {user} = useSelector(state => ({
    user: state.auth.user,
  }));
  const [dataCheckout, setDataCheckout] = useState()
  const {control, handleSubmit} = useForm();

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  const {status, data, error, refetch, isLoading} = useQuery(
    ['getCheckOutTemp', {userid: user?.id}],
    () => fetch(user?.id),
    {
      cacheTime: 0,
      staleTime: 0,
    },
  );
  useEffect(()=>{
    setDataCheckout(data)
    if(data?.info?.payment?.list_coupon?.value && data?.info?.payment?.list_coupon?.value.length > 0){
      dispatch(
        updatelistVoucher({
          voucher_code: data?.info?.payment?.list_coupon?.value.join(','),
        }),
      );
    }else{
      dispatch(
        updatelistVoucher({
          voucher_code: '',
        }),
      );
    }
  },[data])
  console.log('---------Data checkout------')
  console.log(dataCheckout)
  function onUpdatePayment() {
    refetch();
  }
  if (!onReady && isLoading) {
    return (
      <View
        style={{
          justifyContent: 'center',
          alignItems: 'center',
          margin: spacing.large,
          flex: 1,
        }}>
        <Spinner type="Circle" color={colors.primary} size={40} />
      </View>
    );
  }

  const _renderForm = (
    <View style={styles.section}>
      <AddressDelivery data={data?.address} onUpdatePayment={onUpdatePayment} />
    </View>
  );

  const _renderInfoCart = (
    <View style={styles.section}>
      <Text style={styles.headerTitle}>Sản phẩm</Text>
      <PaymentProducts products={data?.items} payments={data?.info} saleAuto={data?.gift_order_auto} />
    </View>
  );

  const _renderDiscountCode = (
    <View style={styles.section}>
      <Text style={styles.headerTitle}>Mã khuyến mãi</Text>
      <Discount
        id={data?.info?.id}
        userId={user?.id}
        counpons={data?.coupon}
        onToggleCoupon={() => refetch()}
      />
    </View>
  );

  const _renderPaymentMethod = (
    <View style={styles.section}>
      <Text style={styles.headerTitle}>Hình thức thanh toán</Text>
      <PaymentMethod
        typePayment={data?.info?.type_payment}
        payments={data?.payments}
        userId={user?.id}
      />
    </View>
  );

  const _renderPointMethod = () => {
    if (data?.points && data?.points?.point_to_money > 0)
      return (
        <>
          <View style={styles.section}>
            <Text style={styles.headerTitle}>Thanh toán bằng điểm</Text>
            <Point
              points={data?.points}
              userId={user?.id}
              pointPayment={data?.info?.point_payment}
              onUpdatePayment={onUpdatePayment}
            />
          </View>
          <Divider />
        </>
      );
    return null;
  };
  return (
    <>
      <ScrollView
        style={styles.box}
        contentContainerStyle={{flexGrow: 1}}
        showsVerticalScrollIndicator={false}>
        {_renderForm}
        {_renderPointMethod()}
        {_renderPaymentMethod}
        {_renderInfoCart}
        {_renderDiscountCode}
      </ScrollView>
      <PaymentFooter
        totalItem={data?.items?.length || 0}
        totalPrice={data?.info?.payment?.total?.value || 0}
        products={data?.items}
        userId={user?.id}
      />
      <AddressModal
        visible={modalVisible}
        onClose={() => setModalVisible(false)}
      />
    </>
  );
};

const styles = StyleSheet.create({
  box: {
    backgroundColor: '#fff',
    paddingVertical: 10,
  },
  headerTitle: {
    color: '#000000',
    fontSize: 16,
    fontWeight: '500',
  },
  footerDiscount: {
    color: '#0F83FF',
    fontSize: 13,
    lineHeight: 18,
    padding: 10,
  },
  inputContainer: {
    marginVertical: 5,
  },
  inputLabelTxt: {
    fontSize: 14,
    fontWeight: '300',
    color: '#2a2a2a',
  },
  inputStyle: {
    fontSize: 13,
    color: '#888',
  },
  section: {
    padding: 10,
    elevation: 4,
    backgroundColor: '#fff',
    marginBottom: 10,
  },
});

export const PaymentScreen = Screen;
