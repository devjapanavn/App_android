import { Button, Divider, Input, Text } from 'react-native-elements';
import { Controller, useForm } from 'react-hook-form';
import React, { useState } from 'react';
import { ScrollView, StatusBar, StyleSheet, View } from 'react-native';
import { gobackRoute, resetAndNavigateRoute, resetRoute } from '@app/route';
import { useDispatch, useSelector } from 'react-redux';

import { ROUTES } from '@app/constants';
import { SafeAreaView } from 'react-native-safe-area-context';
import api from '@app/api';
import { globalStyles } from '@app/assets';
import messaging from '@react-native-firebase/messaging';
import { onLogout } from '@app/store/auth/services';
import { toastAlert } from '@app/utils';

const Screen = props => {
  const { user } = useSelector(state => ({
    user: state.auth.user,
  }));
  const [isShowOldPass, setIsShowOldPass] = useState(false);
  const [isShowNewPass, setIsShowNewPass] = useState(false);
  const [isShowConfirmPass, setIsShowConfirmPass] = useState(false);
  const {
    control,
    handleSubmit,
    formState: { errors },
  } = useForm();
  const dispatch = useDispatch();
  const [loading, setLoading] = useState(false);

  const onSubmit = async data => {
    const dt = {
      id: user?.id,
      password: data.old_password,
      password_new: data.new_password,
      repassword_new: data.confirm_password,
    };
    try {
      setLoading(true);
      const res = await api.changePass(dt);
      setLoading(false);
      toastAlert('Đã thay đổi mật khẩu');
      resetAndNavigateRoute([
        { name: ROUTES.MAIN_TABS },
        { name: ROUTES.LOGIN },
      ]);
      dispatch(onLogout(user.id)).then(async res => {
        const authorizationStatus = await messaging().requestPermission();
        if (authorizationStatus) {
          const token = await messaging().getToken();
          await api.pushToken(null, token)
        }
      });
    } catch (error) {
      setLoading(false);
    }
  };

  const renderFooter = () => {
    return (
      <View>
        <Divider />
        <Button
          loading={loading}
          disabled={loading}
          onPress={handleSubmit(onSubmit)}
          title="Cập nhật"
          containerStyle={styles.buttonContainer}
          titleStyle={{ fontSize: 17 }}
          buttonStyle={{ backgroundColor: '#2367ff', borderRadius: 4 }}
        />
      </View>
    );
  };

  return (
    <SafeAreaView style={styles.box}>
      <StatusBar barStyle="light-content" backgroundColor="#dc0000" />
      <ScrollView>
        <View
          style={{
            alignItems: 'center',
          }}></View>
        <Controller
          control={control}
          rules={{ required: true }}
          render={({ field: { onChange, onBlur, value } }) => (
            <Input
              containerStyle={styles.inputContainer}
              label={
                <Text style={styles.inputLabelTxt}>
                  <Text style={{ color: 'red' }}>*</Text> Mật khẩu cũ
                </Text>
              }
              autoFocus
              autoCapitalize="none"
              textContentType="password"
              secureTextEntry={!isShowOldPass}
              onBlur={onBlur}
              placeholder="Nhập mật khẩu cũ"
              placeholderTextColor="#888"
              inputStyle={styles.inputStyle}
              onChangeText={onChange}
              autoCorrect={false}
              value={value}
              rightIcon={{
                name: isShowOldPass ? 'eye-off-sharp' : 'eye-sharp',
                type: 'ionicon',
                onPress: () => setIsShowOldPass(prev => !prev),
              }}
              errorMessage={
                errors.old_password ? 'Vui lòng nhập mật khẩu cũ ' : null
              }
            />
          )}
          name="old_password"
        />
        <Controller
          control={control}
          rules={{ required: true }}
          render={({ field: { onChange, onBlur, value } }) => (
            <Input
              containerStyle={styles.inputContainer}
              label={
                <Text style={styles.inputLabelTxt}>
                  <Text style={{ color: 'red' }}>*</Text> Mật khẩu mới
                </Text>
              }
              autoCapitalize="none"
              textContentType="password"
              secureTextEntry={!isShowNewPass}
              onBlur={onBlur}
              placeholder="Nhập mật khẩu mới"
              placeholderTextColor="#888"
              inputStyle={styles.inputStyle}
              onChangeText={onChange}
              autoCorrect={false}
              value={value}
              rightIcon={{
                name: isShowOldPass ? 'eye-off-sharp' : 'eye-sharp',
                type: 'ionicon',
                onPress: () => setIsShowNewPass(prev => !prev),
              }}
              errorMessage={
                errors.new_password ? 'Vui lòng nhập mật khẩu mới ' : null
              }
            />
          )}
          name="new_password"
        />
        <Controller
          control={control}
          rules={{ required: true }}
          render={({ field: { onChange, onBlur, value } }) => (
            <Input
              containerStyle={styles.inputContainer}
              label={
                <Text style={styles.inputLabelTxt}>
                  <Text style={{ color: 'red' }}>*</Text> Xác nhận mật khẩu
                </Text>
              }
              autoCapitalize="none"
              textContentType="password"
              secureTextEntry={!isShowConfirmPass}
              onBlur={onBlur}
              placeholder="Xác nhận lại mật khẩu"
              placeholderTextColor="#888"
              inputStyle={styles.inputStyle}
              onChangeText={onChange}
              autoCorrect={false}
              value={value}
              rightIcon={{
                name: isShowOldPass ? 'eye-off-sharp' : 'eye-sharp',
                type: 'ionicon',
                onPress: () => setIsShowConfirmPass(prev => !prev),
              }}
              errorMessage={
                errors.confirm_password ? 'Vui lòng nhập lại mật khẩu ' : null
              }
            />
          )}
          name="confirm_password"
        />
      </ScrollView>
      {renderFooter()}
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
    color: '#888',
  },
  buttonContainer: {
    margin: 10,
  },
  titleStyle: {
    ...globalStyles.text,
  },
});

export const ChangePassScreen = Screen;
