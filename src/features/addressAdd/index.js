import api from '@app/api';
import { AddressModal } from '@app/components';
import { gobackRoute } from '@app/route';
import { useRoute } from '@react-navigation/native';
import _ from 'lodash';
import React, { useEffect, useState } from 'react';
import { stringHelper, toastAlert } from '@app/utils';
import { Controller, useForm } from 'react-hook-form';
import {
  StyleSheet,
  InteractionManager,
  StatusBar,
  View,
  TouchableOpacity,
  ScrollView,
} from 'react-native';
import { Button, CheckBox, Divider, Input, Text } from 'react-native-elements';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useQuery } from 'react-query';
import { useSelector } from 'react-redux';

const Screen = props => {
  const route = useRoute();
  const [onReady, setOnReady] = useState(false);
  const [addressInfo, setAddressInfo] = useState({ ...route.params } || null);
  const {
    control,
    handleSubmit,
    formState: { errors },
  } = useForm();
  const [modalVisible, setModalVisible] = useState(false);
  const [isLoading, setLoading] = useState(false);
  const { user } = useSelector(state => ({
    user: state.auth.user,
  }));
  console.log('route?.params?.onRefresh', route?.params);

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  const fetchgetDetailAddress = async key => {
    return await api.getDetailAddress(user?.id, route.params?.id);
  };

  const { status, data, error, refetch } = useQuery(
    ['getDetailAddress', { id: route.params?.id }],
    fetchgetDetailAddress,
    { enabled: !_.isEmpty(route.params?.id) },
  );

  useEffect(() => {
    if (data) {
      setAddressInfo(data);
    }
  }, [data]);

  const onSubmit = async data => {
    if (onValidate()) {
      const dataSubmit = {
        member_id: user?.id,
        province_id: addressInfo.province_id,
        district_id: addressInfo.district_id,
        ward_id: addressInfo.ward_id,
        note: '',
        province: addressInfo.province,
        district: addressInfo.district,
        ward: addressInfo.ward,
        fullname: data.fullname,
        address: data.address,
        mobile: data.phone,
        id: addressInfo?.id || 0,
        default: addressInfo.default || '0',
      };
      setLoading(true);
      try {
        let res = null;
        if (addressInfo?.id) {
          res = await api.updateAddress(dataSubmit);
          toastAlert('Sửa địa chỉ thành công.');
        } else {
          res = await api.addAddress(dataSubmit);
          toastAlert('Thêm địa chỉ thành công.');
        }
        setLoading(false);
        if (route?.params?.onRefresh) {
          route?.params?.onRefresh();
        }
        if (route?.params?.onSelect) {
          route?.params?.onSelect(res);
        }
        gobackRoute();
      } catch (error) {
        setLoading(false);
      }
    }
  };

  function onValidate() {
    if (stringHelper.formatToNumber(addressInfo?.province_id || 0) === 0) {
      toastAlert('Bạn chưa chọn Tỉnh/Thành phố');
      return false;
    }
    if (stringHelper.formatToNumber(addressInfo?.district_id || 0) === 0) {
      toastAlert('Bạn chưa chọn Quận/Huyện');
      return false;
    }
    if (stringHelper.formatToNumber(addressInfo?.ward_id || 0) === 0) {
      toastAlert('Bạn chưa chọn Phường/Xã');
      return false;
    }
    return true;
  }

  function toggleDefault() {
    if (addressInfo && addressInfo.default === '1') {
      setAddressInfo(prev => ({ ...prev, default: '0' }));
    } else {
      setAddressInfo(prev => ({ ...prev, default: '1' }));
    }
  }

  function onChangeLocation(address) {
    setAddressInfo(address);
    setTimeout(() => {
      setModalVisible(false);
    }, 300);
  }
  const renderFooter = () => {
    return (
      <View>
        <Divider />
        <Button
          onPress={handleSubmit(onSubmit)}
          title={addressInfo?.id ? 'Chỉnh sửa' : 'Thêm mới'}
          containerStyle={styles.buttonContainer}
          titleStyle={{ fontSize: 17 }}
          loading={isLoading}
          disabled={isLoading}
          buttonStyle={{ backgroundColor: '#2367ff', borderRadius: 4 }}
        />
      </View>
    );
  };

  return (
    <SafeAreaView style={styles.box}>
      <StatusBar barStyle="light-content" backgroundColor="#dc0000" />
      <ScrollView>
        <Controller
          control={control}
          rules={{ required: true }}
          render={({ field: { onChange, onBlur, value } }) => (
            <Input
              containerStyle={styles.inputContainer}
              label={
                <Text style={styles.inputLabelTxt}>
                  <Text style={{ color: 'red' }}>*</Text> Họ và tên
                </Text>
              }
              onBlur={onBlur}
              placeholder="Nhập họ và tên"
              placeholderTextColor="#888"
              inputStyle={styles.inputStyle}
              onChangeText={onChange}
              autoCorrect={false}
              value={value}
              errorMessage={
                errors.fullname ? 'Vui lòng nhập tên người nhận hàng' : null
              }
            />
          )}
          name="fullname"
          defaultValue={addressInfo?.fullname || ''}
        />
        <Controller
          control={control}
          rules={{ required: true }}
          render={({ field: { onChange, onBlur, value } }) => (
            <Input
              containerStyle={styles.inputContainer}
              label={
                <Text style={styles.inputLabelTxt}>
                  <Text style={{ color: 'red' }}>*</Text> Số điện thoại
                </Text>
              }
              onBlur={onBlur}
              placeholder="Nhập số điện thoại"
              placeholderTextColor="#888"
              keyboardType="phone-pad"
              inputStyle={styles.inputStyle}
              onChangeText={onChange}
              autoCorrect={false}
              value={value}
              errorMessage={errors.phone ? 'Vui lòng nhập số điện thoại' : null}
            />
          )}
          name="phone"
          defaultValue={addressInfo?.mobile || ''}
        />
        <TouchableOpacity
          onPress={() => {
            setModalVisible(true);
          }}>
          <Input
            disabled
            containerStyle={styles.inputContainer}
            label={
              <Text style={styles.inputLabelTxt}>
                <Text style={{ color: 'red' }}>*</Text> Tỉnh/ Thành
              </Text>
            }
            value={addressInfo?.province || ''}
            placeholder="Chọn"
            placeholderTextColor="#888"
            inputStyle={styles.inputStyle}
            autoCorrect={false}
            rightIcon={{
              name: 'chevron-down-outline',
              type: 'ionicon',
              color: '#888',
            }}
          />
        </TouchableOpacity>
        <View style={{ flexDirection: 'row', justifyContent: 'space-around' }}>
          <TouchableOpacity
            style={{ flex: 1 }}
            onPress={() => {
              setModalVisible(true);
            }}>
            <Input
              disabled
              containerStyle={[styles.inputContainer]}
              label={
                <Text style={styles.inputLabelTxt}>
                  <Text style={{ color: 'red' }}>*</Text> Quận/ Huyện
                </Text>
              }
              value={addressInfo?.district || ''}
              placeholder="Chọn"
              placeholderTextColor="#888"
              inputStyle={styles.inputStyle}
              autoCorrect={false}
              rightIcon={{
                name: 'chevron-down-outline',
                type: 'ionicon',
                color: '#888',
              }}
            />
          </TouchableOpacity>
          <TouchableOpacity
            style={{ flex: 1 }}
            onPress={() => {
              setModalVisible(true);
            }}>
            <Input
              disabled
              containerStyle={[styles.inputContainer]}
              label={
                <Text style={styles.inputLabelTxt}>
                  <Text style={{ color: 'red' }}>*</Text> Phường/ xã
                </Text>
              }
              value={addressInfo?.ward || ''}
              placeholder="Chọn"
              placeholderTextColor="#888"
              inputStyle={styles.inputStyle}
              autoCorrect={false}
              rightIcon={{
                name: 'chevron-down-outline',
                type: 'ionicon',
                color: '#888',
              }}
            />
          </TouchableOpacity>
        </View>
        <Controller
          control={control}
          rules={{ required: true }}
          render={({ field: { onChange, onBlur, value } }) => (
            <Input
              containerStyle={styles.inputContainer}
              label={
                <Text style={styles.inputLabelTxt}>
                  <Text style={{ color: 'red' }}>*</Text> Địa chỉ
                </Text>
              }
              onBlur={onBlur}
              placeholder="Nhập địa chỉ"
              placeholderTextColor="#888"
              inputStyle={styles.inputStyle}
              onChangeText={onChange}
              autoCorrect={false}
              value={value}
              errorMessage={errors.address ? 'Vui lòng nhập địa chỉ' : null}
            />
          )}
          name="address"
          defaultValue={addressInfo?.address || ''}
        />

        <CheckBox
          title="Đặt làm mặc đinh"
          checked={addressInfo?.default === '1'}
          onPress={toggleDefault}
          iconType="material"
          checkedIcon="check-box"
          uncheckedIcon="check-box-outline-blank"
          containerStyle={{
            backgroundColor: 'transparent',
            borderWidth: 0,
            paddingVertical: 0,
          }}
          textStyle={{
            fontSize: 14,
            fontFamily: 'SF Pro Display',
            fontWeight: 'normal',
          }}
        />
      </ScrollView>
      {renderFooter()}
      <AddressModal
        address={addressInfo}
        visible={modalVisible}
        onChange={onChangeLocation}
        onClose={() => setModalVisible(false)}
      />
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  box: {
    flex: 1,
    backgroundColor: '#fff',
  },
  inputContainer: {
    marginVertical: 5,
  },
  inputLabelTxt: {
    fontSize: 12,
    color: '#3b4859',
  },
  inputStyle: {
    fontSize: 13,
  },
  buttonContainer: {
    margin: 10,
  },
});

export const AddressAddScreen = Screen;
