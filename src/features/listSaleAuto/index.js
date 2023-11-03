import api from '@app/api';
import {ROUTES} from '@app/constants';
import {gobackRoute, navigateRoute} from '@app/route';
import {Controller, useForm} from 'react-hook-form';
import {useRoute} from '@react-navigation/native';
import _, {round} from 'lodash';
import React, {useEffect, useState} from 'react';
import {
  StyleSheet,
  StatusBar,
  FlatList,
  View,
} from 'react-native';
import {Text, Rating} from 'react-native-elements';
import {BottomSheet, Button, Divider, ListItem} from 'react-native-elements';
import {SafeAreaView} from 'react-native-safe-area-context';
import {useSelector} from 'react-redux';
import {SaleAutoItem} from './component';


const Screen = props => {

  const {user} = useSelector(state => ({
    user: state.auth.user,
  }));
  const route = useRoute();

  const [voucherDetails, setVoucherDetails] = useState();

  useEffect(() => {
    setVoucherDetails(route.params.data);
  }, [route.params.data]);

  const changeCheckbox = obData => {
    let data = voucherDetails.map((item, index) => {
      if (item.check_active == 1) {
        item.check_active = 0;
      }
      if (obData.id_product == item.id_product) {
        item.check_active = 1;
      }
      return item;
    });
    setVoucherDetails(data);
  };

  const applyGiftSaleAuto = () => {
    route.params?.onSelect(voucherDetails)
    gobackRoute()
  }
  const renderFooter = () => {
    return (
      <View style={styles.footerBtn}>
        <Divider />
        <Button
          title="Áp dụng"
          containerStyle={styles.buttonContainer}
          titleStyle={{fontSize: 17}}
          buttonStyle={{backgroundColor: '#2367ff', borderRadius: 4}}
          onPress={applyGiftSaleAuto}
        />
      </View>
    );
  };
  return (
    <SafeAreaView style={styles.box}>
      <StatusBar barStyle="light-content" backgroundColor="#dc0000" />
      <FlatList
        data={voucherDetails}
        key="sale_auto"
        keyExtractor={item => `sale_auto_${item.id_product}`}
        renderItem={({item}) => (
          <SaleAutoItem data={item} onPress={changeCheckbox} />
        )}
      />
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
    fontSize: 15,
  },
  warpSearchInput: {
    flexDirection: 'row',
    flex: 1,
    borderColor: '#d9d9d9',
    borderWidth: 1,
    borderRadius: 4,
    alignItems: 'center',
  },
  btnSearch: {
    backgroundColor: '#2266ff',
    marginHorizontal: 10,
    borderRadius: 2,
    height: 35,
    width: 80,
  },
  boxSearchVoucher: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: -30,
    marginLeft: 10,
    marginBottom: 10,
  },
  box: {
    flex: 1,
    backgroundColor: '#fff',
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

export const ListSaleAutoScreen = Screen;
