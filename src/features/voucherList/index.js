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
  TouchableOpacity,
} from 'react-native';
import {Text, Rating} from 'react-native-elements';
import {BottomSheet, Button, Divider, ListItem} from 'react-native-elements';
import {SafeAreaView} from 'react-native-safe-area-context';
import Spinner from 'react-native-spinkit';
import {useQuery} from 'react-query';
import {useSelector} from 'react-redux';
import {VoucherItem} from './component';
import {toastAlert} from '@app/utils';

const fetch = async userId => {
  return api.getCheckOutTemp(userId);
};
const Screen = props => {
  const {
    control,
    handleSubmit,
    reset,
    formState: {errors},
  } = useForm();
  const route = useRoute();
  const [onReady, setOnReady] = useState(false);
  const [OnShow, setOnShow] = useState(false);
  const [IconDropDown, setIconDropDown] = useState({
    type: 'ionicon',
    name: 'chevron-up-sharp',
    size: 15,
  });
  const [list, setList] = useState([]);
  const [listVoucherCode, setListVoucherCode] = useState([]);

  const {user, voucher_code} = useSelector(state => ({
    user: state.auth.user,
    voucher_code: state.checkout.voucher_code?.split(','),
  }));
  // console.log('voucher_code')
  // console.log(voucher_code)
  const {status, data, error, refetch, isLoading} = useQuery(
    ['getCheckOutTemp', {userId: user?.id}],
    () => fetch(user?.id),
    {enabled: onReady, cacheTime: 0, staleTime: 0},
  );

  const setVoucherAnother = (voucherCode, checkNull = 0) => {
    let checkCode = listVoucherCode.filter(item1 => item1 == voucherCode);
    if (checkCode && checkCode.length > 0) {
      if(!checkNull){
        setListVoucherCode(listVoucherCode.filter(item1 => item1 != voucherCode));
      }
    } else {
      setListVoucherCode([...listVoucherCode, voucherCode]);
    }
  };

  const onSubmit = async data => {
    let res = await api.getVoucherAnother(data.counpon);
    if (res.message) {
      toastAlert(res.message);
    }else{
      setListVoucherCode([...listVoucherCode, data.counpon]);
    }
  };
  const applyListVoucher = () => {
    route.params?.onSelect(listVoucherCode);
    gobackRoute();
  };
  console.log('listVoucherCode')
  console.log(listVoucherCode)
  useEffect(() => {
    console.log('data')
    console.log(data)
    loadListVoucherData(data)
  }, [data,OnShow]);
 
  const loadListVoucherData = (dataV) => {
    if (dataV && OnShow) {
      setIconDropDown({
        type: 'ionicon',
        name: 'chevron-up-sharp',
        size: 15,
      })
      setList(dataV.voucher_list);
      setList([
        {
          title: 'Voucher được áp dụng',
          data:  filterListVoucher(dataV.voucher_list, 1),
        },
        {
          title: 'Voucher không được áp dụng',
          data: filterListVoucher(dataV.voucher_list, 0),
        },
      ]);
    }else if(dataV && !OnShow){
      setIconDropDown({
        type: 'ionicon',
        name: 'chevron-down-sharp',
        size: 15,
      })
      setList([
        {
          title: 'Voucher được áp dụng',
          data: filterListVoucher(dataV.voucher_list, 1),
        },
        {
          title: 'Voucher không được áp dụng',
          data: [],
        },
      ]);
    }
  }
  // console.log(list)
  const filterListVoucher = (listVoucher, check = 0) => {
    let listvoucher = [];
    let listVoucherCodes = []
    listVoucher.forEach((item, index) => {
      if (item.is_check_ap_dung == check && item.is_check_hien_thi == 1) {
        if(voucher_code.length > 0){
          let voucherCodes = voucher_code.find((itemVoucher)=> item.code == itemVoucher)
          if(voucherCodes){
            item.checked = true
            listVoucherCodes.push(voucherCodes)
          }else{
            item.checked = false
          }
        }

        listvoucher.push(item);
      }
    });
    setListVoucherCode(listVoucherCodes);
    return listvoucher;
  };

  const dropdownVoucherList = () => {
    setOnShow(!OnShow)
  }
  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);
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
  const renderFooter = () => {
    return (
      <View style={styles.footerBtn}>
        <Divider />
        <Button
          title="Áp dụng"
          containerStyle={styles.buttonContainer}
          titleStyle={{fontSize: 17}}
          buttonStyle={{backgroundColor: '#2367ff', borderRadius: 4}}
          onPress={applyListVoucher}
        />
      </View>
    );
  };
  return (
    <SafeAreaView style={styles.box}>
      <StatusBar barStyle="light-content" backgroundColor="#dc0000" />
      <View style={styles.boxSearchVoucher}>
        <View style={styles.warpSearchInput}>
          <Image
            source={images.ic_discount}
            style={{width: 28, height: 28, margin: 3}}
            resizeMode="contain"
          />
          <Controller
            control={control}
            rules={{required: false}}
            name="counpon"
            defaultValue={''}
            render={({field: {onChange, onBlur, value}}) => (
              <TextInput
                style={{height: 35, flex: 1,textTransform: 'uppercase'}}
                autoCapitalize={'characters'}
                placeholder="Nhập mã giảm giá"
                placeholderTextColor="#888"
                onChangeText={onChange}
                autoComplete="off"
                autoCorrect={false}
                value={value}
              />
            )}
          />
        </View>
        <Button
          onPress={handleSubmit(onSubmit)}
          loading={isLoading}
          buttonStyle={styles.btnSearch}
          titleStyle={{fontSize: 13}}
          title={'Áp dụng'}
        />
      </View>

      <SectionList
        style={{marginBottom: 60}}
        sections={list}
        keyExtractor={(item, index) => item.promotion_id + index}
        renderItem={({item}) => {
          if(item && item.is_check_hien_thi == 1){
            if(item?.textErr){
              return <View>
              <Text style={styles.VoucherTitle}>{item?.textErr}</Text>
            </View>
            }else{
              return <VoucherItem data={item} onPress={setVoucherAnother} />
            }
          }
          
        }}
        renderSectionHeader={({section: {title}}) => (
          (title == 'Voucher không được áp dụng')?
          <Button
            buttonStyle={styles.VoucherTitle}
            type={'clear'}
            title={title}
            titleStyle={{color: '#000', textAlign: 'left', fontSize: 15, paddingLeft: 10, width: '100%'}}
            iconRight
            onPress={dropdownVoucherList}
            icon={IconDropDown}
          />
          : 
          <View>
            <Text style={styles.VoucherTitle}>{title}</Text>
          </View>
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
    textAlign: 'left',
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

export const VoucherListScreen = Screen;
