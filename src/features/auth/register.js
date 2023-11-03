import api from '@app/api';
import { appDimensions, globalStyles, images } from '@app/assets';
import { ImageReponsive } from '@app/components';
import React, { useCallback, useEffect, useRef, useState } from 'react';
import { Controller, useForm } from 'react-hook-form';
import {
  StyleSheet,
  InteractionManager,
  View,
  StatusBar,
  Keyboard,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Button, Icon, Input } from 'react-native-elements';
import { ScrollView } from 'react-native-gesture-handler';
import { iOSColors } from 'react-native-typography';
import { gobackRoute, navigateRoute, resetRoute } from '@app/route';
import { useDispatch } from 'react-redux';
import { ROUTES } from '@app/constants';
import { toastAlert } from '@app/utils';
import { useRoute } from '@react-navigation/native';

const Screen = props => {
  const [onReady, setOnReady] = useState(false);
  const route = useRoute()
  const dispatch = useDispatch();
  const {
    control,
    handleSubmit,
    formState: { errors },
  } = useForm();
  const [isShowPass, setShowPass] = useState(false);
  const [isShowConfirmPass, setShowConfirmPass] = useState(false);
  const [isLoading, setLoading] = useState(false);
  const passwordRef = useRef(null);
  const usernameRef = useRef(null);
  const passwordConfirmRef = useRef(null);
  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  const handleShowPass = useCallback(() => {
    setShowPass(val => !val);
  }, [isShowPass]);

  const handlePressBack = useCallback(() => {
    gobackRoute();
  }, []);

  const onSubmit = async data => {
    Keyboard.dismiss();
    setLoading(true);
    try {
      const res = await api.register(data.fullname, route.params?.phone, data.password, data.passwordConfirm);
      setLoading(false);
      navigateRoute(ROUTES.LOGIN);
      setTimeout(() => {
        toastAlert("Đăng ký thành công!")
      }, 300);
    } catch (error) {
      console.log('error', error);
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.box}>
      <StatusBar barStyle="light-content" backgroundColor="#fff" />

      <ScrollView
        contentContainerStyle={styles.container}
        keyboardShouldPersistTaps="handled">
        {onReady ? (
          <>
            <View style={{ width: '100%', paddingHorizontal: 20 }}>
              <Controller
                control={control}
                rules={{
                  required: true,
                }}
                render={({ field: { onChange, onBlur, value } }) => (
                  <Input
                    autoFocus
                    autoCapitalize="none"
                    containerStyle={styles.inputContainer}
                    onBlur={onBlur}
                    placeholder="Họ và tên"
                    placeholderTextColor="#888"
                    inputStyle={styles.inputStyle}
                    onChangeText={onChange}
                    autoCorrect={false}
                    value={value}
                    returnKeyType="next"
                    errorMessage={
                      errors.fullname ? 'Vui lòng nhập tên đăng nhập' : null
                    }
                    onSubmitEditing={() => usernameRef.current.focus()}
                  />
                )}
                name="fullname"
                defaultValue=""
              />
              {/* <Controller
                control={control}
                rules={{
                  required: true,
                }}
                render={({ field: { onChange, onBlur, value } }) => (
                  <Input
                    ref={usernameRef}
                    autoCapitalize="none"
                    containerStyle={styles.inputContainer}
                    onBlur={onBlur}
                    placeholder="Số điện thoại"
                    placeholderTextColor="#888"
                    inputStyle={styles.inputStyle}
                    onChangeText={onChange}
                    autoCorrect={false}
                    keyboardType="phone-pad"
                    textContentType="telephoneNumber"
                    value={value}
                    returnKeyType="next"
                    errorMessage={
                      errors.username ? 'Vui lòng nhập số điện thoại' : null
                    }
                    onSubmitEditing={() => passwordRef.current.focus()}
                  />
                )}
                name="phone"
              /> */}
              <Controller
                control={control}
                rules={{
                  required: true,
                }}
                render={({ field: { onChange, onBlur, value } }) => (
                  <Input
                    ref={passwordRef}
                    autoCapitalize="none"
                    containerStyle={styles.inputContainer}
                    onBlur={onBlur}
                    placeholder="Mật khẩu"
                    secureTextEntry={!isShowPass}
                    placeholderTextColor="#888"
                    inputStyle={styles.inputStyle}
                    onChangeText={onChange}
                    autoCorrect={false}
                    value={value}
                    textContentType="password"
                    errorMessage={
                      errors.username ? 'Vui lòng nhập mật khẩu' : null
                    }
                    onSubmitEditing={() => passwordConfirmRef.current.focus()}
                    rightIcon={{
                      name: isShowPass ? 'eye-off-sharp' : 'eye-sharp',
                      type: 'ionicon',
                      onPress: handleShowPass,
                    }}
                  />
                )}
                name="password"
                defaultValue=""
              />
              <Controller
                control={control}
                rules={{
                  required: true,
                }}
                render={({ field: { onChange, onBlur, value } }) => (
                  <Input
                    ref={passwordConfirmRef}
                    autoCapitalize="none"
                    containerStyle={styles.inputContainer}
                    onBlur={onBlur}
                    placeholder="Xác nhận lại mật khẩu"
                    secureTextEntry={!isShowConfirmPass}
                    placeholderTextColor="#888"
                    inputStyle={styles.inputStyle}
                    onChangeText={onChange}
                    autoCorrect={false}
                    value={value}
                    textContentType="password"
                    errorMessage={
                      errors.passwordConfirm ? 'Vui lòng nhập lại mật khẩu' : null
                    }
                    onSubmitEditing={handleSubmit(onSubmit)}
                    rightIcon={{
                      name: isShowConfirmPass ? 'eye-off-sharp' : 'eye-sharp',
                      type: 'ionicon',
                      onPress: () => setShowConfirmPass(prev => !prev),
                    }}
                  />
                )}
                name="passwordConfirm"
                defaultValue=""
              />
              <Button
                disabled={isLoading}
                loading={isLoading}
                title={'Đăng ký'}
                buttonStyle={styles.loginButton}
                onPress={handleSubmit(onSubmit)}
              />
            </View>
            <View></View>
          </>
        ) : (
          <>
            <View />
            <View />
          </>
        )}
      </ScrollView>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  box: {
    flex: 1,
    backgroundColor: '#fff',
  },
  container: {
    flexGrow: 1,
    alignItems: 'center',
    justifyContent: 'space-around',
  },
  logo: {
    width: 200,
    marginVertical: 20,
  },
  title: {
    fontSize: 16,
  },
  inputStyle: {
    ...globalStyles.text,
    fontSize: 13,
    color: '#888',
  },
  inputContainer: {
    marginVertical: 0,
  },
  loginButton: {
    backgroundColor: '#3b7cff',
  },
  footerLogin: {
    justifyContent: 'space-between',
    marginVertical: 10,
  },
  footerLoginText: {
    ...globalStyles.text,
    fontSize: 12,
    color: iOSColors.blue,
  },
  backContainer: {
    position: 'absolute',
    top: 10,
    left: 10,
    zIndex: 20,
  },
});

export const RegisterScreen = Screen;
