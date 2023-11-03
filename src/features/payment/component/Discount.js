import api from '@app/api';
import {colors, globalStyles, images} from '@app/assets';
import React, {useState} from 'react';
import {Controller, useForm} from 'react-hook-form';
import {Image, StyleSheet, Text, TextInput, View, TouchableOpacity} from 'react-native';
import {Button, Chip, Icon} from 'react-native-elements';
import {iOSColors} from 'react-native-typography';
import { ROUTES } from '@app/constants';
import { navigateRoute } from '@app/route';
import { toastAlert } from '@app/utils';
import { useSelector } from 'react-redux';
import { ScrollView } from 'react-native';

export const Discount = React.memo(
  ({id, userId, counpons, onToggleCoupon}) => {
    const {
      control,
      handleSubmit,
      reset,
      formState: {errors},
    } = useForm();
    const [isLoading, setLoading] = useState(false);
     const {voucher_code} = useSelector(state => ({
      voucher_code: state.checkout.voucher_code?.split(','),
    }))
    // const onSubmit = async data => {
    //   try {
    //     setLoading(true);
    //     const res = await api.checkCoupon(userId, id, data.counpon);
    //     setLoading(false);
    //     reset({counpon: ''});
    //     onToggleCoupon();
    //   } catch (error) {
    //     setLoading(false);
    //     console.log(error);
    //   }
    // };
    const changeVoucher = () =>{
      navigateRoute(ROUTES.VOUCHER_LIST, { onSelect }, false, true);
    }
    async function onSelect(voucherCode) {
      if (voucherCode) {
        try {
          let res =  await api.getVoucherAnother(voucherCode.toString())
          onToggleCoupon();
        } catch (error) {
          console.log(error)
        }
      }
    }
    async function removeCoupon(code){
     

      try {
        let voucherFilter = voucher_code.filter((item) => code != item)
        console.log(voucherFilter.toString())
        await api.getVoucherAnother(voucherFilter.toString())
        onToggleCoupon();
      } catch (error) {
        console.log(error)
      }

      // console.log(voucherFilter.join(','))
    }
    return (
      <>
        <TouchableOpacity
          onPress={changeVoucher}
          style={{
            flexDirection: 'row',
            justifyContent: 'center',
            marginVertical: 10,
          }}>
          <View
            style={{
              flexDirection: 'row',
              flex: 1,
              borderColor: '#d9d9d9',
              borderWidth: 1,
              borderRadius: 4,
              alignItems: 'center',
            }}>
            <Image
              source={images.ic_discount}
              style={{width: 28, height: 28, margin: 3}}
              resizeMode="contain"
            />
            <Controller
              control={control}
              rules={{required: false}}
              render={({field: {onChange, onBlur, value}}) => (
                <TextInput
                  editable={false}
                  style={{height: 35, flex: 1}}
                  onBlur={onBlur}
                  autoCapitalize={'characters'}
                  placeholder="Nhập mã giảm giá"
                  placeholderTextColor="#888"
                  onChangeText={onChange}
                  autoComplete="off"
                  autoCorrect={false}
                  value={value}
                />
              )}
              name="counpon"
              defaultValue={''}
            />
          </View>
          <Button
            loading={isLoading}
            buttonStyle={{
              backgroundColor: '#2266ff',
              marginHorizontal: 10,
              borderRadius: 2,
              height: 35,
              width: 80,
            }}
            onPress={changeVoucher}
            titleStyle={{fontSize: 13}}
            title={'Áp dụng'}
          />
        </TouchableOpacity>
        {/* {errors.counpon ? (
          <Text style={styles.error}>Bạn chưa nhập mã giảm giá</Text>
        ) : null} */}
        <ScrollView horizontal={true} style={{width: '100%'}}>
          {voucher_code && voucher_code.length > 0 && voucher_code[0] != '' ? (
            <View style={{flexDirection: 'row'}}>
              {voucher_code.map(item => (
                <Chip
                  key={item}
                  onPress={() => removeCoupon(item)}
                  buttonStyle={{
                    borderRadius: 5,
                    borderStyle: 'dashed',
                    padding: 4,
                    marginRight: 8,
                  }}
                  title={item}
                  type="outline"
                  iconRight
                  icon={{type: 'ionicon', name: 'close', color: colors.link}}
                />
              ))}
            </View>
          ) : null}
        </ScrollView>
        
      </>
    );
  },
  (prev, next) =>
    prev.id === next.id &&
    prev.userId === next.userId &&
    prev.id === next.id &&
    prev.counpons === next.counpons,
);

const styles = StyleSheet.create({
  error: {
    ...globalStyles.text,
    color: iOSColors.red,
    fontSize: 12,
  },
});
