import api from '@app/api';
import {ROUTES} from '@app/constants';
import {gobackRoute, navigateRoute} from '@app/route';
import {Controller, useForm} from 'react-hook-form';
import {useRoute} from '@react-navigation/native';
import _, {round} from 'lodash';
import {colors, globalStyles, images} from '@app/assets';
import React, {useEffect, useState} from 'react';
import {
  StyleSheet,
  InteractionManager,
  StatusBar,
  FlatList,
  View,
  Image,
  TextInput,
  SectionList,
} from 'react-native';
import {Text, Rating} from 'react-native-elements';

import {BottomSheet, Button, Divider, ListItem} from 'react-native-elements';
import {SafeAreaView} from 'react-native-safe-area-context';
import Spinner from 'react-native-spinkit';
import {useQuery} from 'react-query';
import {useSelector} from 'react-redux';
import {VoucherHeader} from './component';
import {stringHelper} from '@app/utils';

const fetch = async userId => {
  return api.getCheckOutTemp(userId);
};
const Screen = props => {
  const route = useRoute();
  const {
    control,
    handleSubmit,
    reset,
    formState: {errors},
  } = useForm();
  const [voucherDetails, setVoucherDetails] = useState();
  const [onReady, setOnReady] = useState(false);
  const {user} = useSelector(state => ({
    user: state.auth.user,
  }));
  const applyVoucher = () => {
    route.params.changeCheckbox();
    gobackRoute();
  };
  useEffect(() => {
    setVoucherDetails(route.params.data);
    setOnReady(true);
  }, [route.params.data]);

  const voucherDetailHeader = () => {
    if (voucherDetails) {
      return (
        <>
          <VoucherHeader data={voucherDetails} />
          <View style={{padding: 10}}>
            <Text style={styles.titleVoucher}>Mã Voucher</Text>
            <Text style={styles.paddingY}>{voucherDetails.code}</Text>
            <Text style={styles.titleVoucher}>Ưu đãi</Text>
            <Text style={styles.paddingY}>
              {voucherDetails.str_loai_voucher.trim()}
            </Text>
            {listGiftDetails()}
            <Text style={styles.titleVoucher}>Hạn sử dụng</Text>
            <Text style={styles.paddingY}>{voucherDetails.han_su_dung}</Text>
            <Text style={styles.titleVoucher}>Điều kiện áp dụng</Text>
            <Text style={styles.paddingY}>
              - Đặt tối thiểu:{' '}
              {stringHelper.formatMoney(voucherDetails.dat_toi_thieu)} đ
            </Text>
            <Text style={styles.paddingY}>
              - {voucherDetails.khach_hang_ap_dung}
            </Text>
            <Text style={styles.paddingY}>- {voucherDetails.details}</Text>
            <Text style={styles.paddingY}>- Số lượng ưu đãi có hạn</Text>
            <Text style={styles.paddingY}>
              - Được áp dụng đồng thời cùng các CTKM khác
            </Text>
          </View>
        </>
      );
    }
  };
  const listGiftDetails = () => {
    if (voucherDetails.qua_tang) {
      return (
        <FlatList
          data={voucherDetails.qua_tang}
          key="gift_list_in_voucher"
          ItemSeparatorComponent={() => <View style={{height: 10}}></View>}
          keyExtractor={item => `gift_list_in_voucher_${item.id_product}`}
          renderItem={({item}) => (
            <Text style={styles.paddingY}>
              Quà tặng: [{item.sku}] - {item.product_name}
            </Text>
          )}
        />
      );
    }
  };
  const renderFooter = () => {
    if (voucherDetails && voucherDetails.is_check_ap_dung == 1) {
      return (
        <View style={styles.footerBtn}>
          <Divider />
          <Button
            title="Áp dụng"
            containerStyle={styles.buttonContainer}
            titleStyle={{fontSize: 17}}
            buttonStyle={{backgroundColor: '#2367ff', borderRadius: 4}}
            onPress={applyVoucher}
          />
        </View>
      );
    }
  };

  if (!onReady) {
    return (
      <View
        style={{
          justifyContent: 'center',
          alignItems: 'center',
          flex: 1,
        }}>
        <Spinner type="Circle" color={colors.primary} size={40} />
      </View>
    );
  }

  return (
    <SafeAreaView style={styles.box}>
      <StatusBar barStyle="light-content" backgroundColor="#dc0000" />
      {voucherDetailHeader()}
      {renderFooter()}
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  VoucherTitle: {
    padding: 10,
    borderColor: '#ccc',
    borderWidth: 1,
    borderStyle: 'solid',
    borderLeftWidth: 0,
    borderRightWidth: 0,
  },
  paddingY: {
    paddingTop: 5,
    paddingBottom: 5,
  },
  titleVoucher: {
    fontWeight: '700',
    fontSize: 16,
  },
  box: {
    flex: 1,
    backgroundColor: '#fff',
    position: 'relative',
  },
  title: {
    fontSize: 16,
  },
  buttonContainer: {
    margin: 10,
  },
  deletingOverlay: {
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: 10,
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: 'rgba(0,0,0,0.5)',
  },
  footerBtn: {
    position: 'absolute',
    width: '100%',
    backgroundColor: '#fff',
    bottom: 0,
  },
});

export const VoucherDetailsScreen = Screen;
