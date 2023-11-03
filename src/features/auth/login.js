import { Button, Icon, Input, Text } from 'react-native-elements';
import { Controller, useForm } from 'react-hook-form';
import {
  InteractionManager,
  Keyboard,
  StatusBar,
  StyleSheet,
  View,
} from 'react-native';
import React, { useCallback, useEffect, useRef, useState } from 'react';
import { appDimensions, colors, globalStyles, images } from '@app/assets';
import { getProfile, getTotalCart, login } from '@app/store/auth/services';
import { gobackRoute, navigateRoute, resetRoute } from '@app/route';
import { useIsFocused, useNavigationState } from '@react-navigation/native';

import { ImageReponsive } from '@app/components';
import { ROUTES } from '@app/constants';
import { SafeAreaView } from 'react-native-safe-area-context';
import { ScrollView } from 'react-native-gesture-handler';
import api from '@app/api';
import { iOSColors } from 'react-native-typography';
import messaging from '@react-native-firebase/messaging'
import { useDispatch } from 'react-redux';
const Screen = props => {
  const isFocused = useIsFocused();
  const routesLength = useNavigationState(state => state.routes.length);
  const previousRouteName =
    routesLength > 1
      ? useNavigationState(state => state.routes[state.index - 1].name)
      : null;

  console.log('previousRouteName', previousRouteName);
  const [onReady, setOnReady] = useState(false);
  const dispatch = useDispatch();
  const {
    control,
    handleSubmit,
    formState: { errors },
  } = useForm();
  const [isShowPass, setShowPass] = useState(false);
  const [isLoading, setLoading] = useState(false);
  const passwordRef = useRef(null);

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

  const handlePressOTP = useCallback(() => {
    navigateRoute(ROUTES.LOGIN_OTP);
  }, []);

  async function initFCMToken(userId) {
    const authorizationStatus = await messaging().requestPermission();
    if (authorizationStatus) {
      const token = await messaging().getToken();
      await api.pushToken(userId, token)
    }
  }

  const onSubmit = async data => {
    Keyboard.dismiss();
    setLoading(true);
    try {
      const res = await dispatch(login(data.username, data.password));
      setLoading(false);
      dispatch(getProfile(res.id))
      initFCMToken(res.id)
      if (previousRouteName === ROUTES.MAIN_TABS || previousRouteName === ROUTES.REGISTER || previousRouteName === ROUTES.FORGET_PASS) {
        resetRoute(ROUTES.MAIN_TABS);
      } else {
        gobackRoute();
      }
      setTimeout(() => {
        dispatch(getTotalCart(res.id));
      }, 1000);
    } catch (error) {
      console.log('error', error);
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.box}>
      <StatusBar barStyle="dark-content" backgroundColor="#fff" />
      {onReady && isFocused ? (
        <>
          <Icon
            name={'chevron-back-outline'}
            type="ionicon"
            size={30}
            containerStyle={styles.backContainer}
            onPress={handlePressBack}
          />
          <ScrollView
            contentContainerStyle={styles.container}
            keyboardShouldPersistTaps="handled">
            <ImageReponsive
              source={images.ic_logo}
              containerStyle={styles.logo}
            />

            <View style={{ width: '100%', paddingHorizontal: 20 }}>
              <Controller
                control={control}
                rules={{
                  required: true,
                }}
                render={({ field: { onChange, onBlur, value } }) => (
                  <Input
                    autoCapitalize="none"
                    containerStyle={styles.inputContainer}
                    onBlur={onBlur}
                    placeholder="Số điện thoại"
                    keyboardType="phone-pad"
                    placeholderTextColor="#888"
                    inputStyle={styles.inputStyle}
                    onChangeText={onChange}
                    autoCorrect={false}
                    textContentType="username"
                    value={value}
                    returnKeyType="next"
                    errorMessage={
                      errors.username ? 'Vui lòng nhập tên đăng nhập' : null
                    }
                    onSubmitEditing={() => passwordRef.current.focus()}
                  />
                )}
                name="username"
                defaultValue=""
              />
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
                    onSubmitEditing={handleSubmit(onSubmit)}
                    rightIcon={{
                      name: isShowPass ? 'eye-off-sharp' : 'eye-sharp',
                      type: 'ionicon',
                      color: '#000',
                      onPress: handleShowPass,
                    }}
                  />
                )}
                name="password"
                defaultValue=""
              />
              <Button
                loading={isLoading}
                disabled={isLoading}
                title={'Đăng nhập'}
                buttonStyle={styles.loginButton}
                onPress={handleSubmit(onSubmit)}
              />

              <View style={[globalStyles.row, styles.footerLogin]}>
                <Button
                  disabled={isLoading}
                  title={'Đăng ký'}
                  onPress={() => navigateRoute(ROUTES.REGISTER_OTP)}
                  type="clear"
                  titleStyle={styles.footerLoginText}
                />
                <Button
                  disabled={isLoading}
                  title={'Quên mật khẩu'}
                  type="clear"
                  titleStyle={styles.footerLoginText}
                  onPress={() => navigateRoute(ROUTES.FORGET_PASS_OTP)}
                />
              </View>

              <View>
                <Text
                  style={{
                    ...globalStyles.text,
                    textAlign: 'center',
                    marginBottom: 20,
                    color: '#bbb',
                  }}>
                  Hoặc
                </Text>
                <Button
                  type="outline"
                  disabled={isLoading}
                  title={'Đăng nhập bằng OTP'}
                  buttonStyle={styles.loginOTPButton}
                  titleStyle={{ ...globalStyles.text, color: '#3b7cff' }}
                  onPress={handlePressOTP}
                />
              </View>
            </View>
            <View style={styles.footer}>
              <Text style={styles.footerText}>
                Bằng việc đăng nhập, quý khách đã đồng ý thực hiện mọi giao dịch
                theo{' '}
                <Text style={styles.footerHightlightText} onPress={() => navigateRoute(ROUTES.LIST_STATIC_BLOG)}>
                  Điều khoản bảo mật và chính sách bảo mật của Siêu Thị Nhật Bản
                  Japana
                </Text>
              </Text>
            </View>
          </ScrollView>
        </>
      ) : (
        <>
          <View />
        </>
      )}
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
    tintColor: '#C90005'
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
    flex: 1,
  },
  loginButton: {
    backgroundColor: '#3b7cff',
  },
  loginOTPButton: {
    borderColor: '#3b7cff',
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
    top: 30,
    left: 20,
    zIndex: 20,
  },
  footer: {
    marginVertical: 10,
    marginHorizontal: 20,
  },
  footerText: {
    ...globalStyles.text,
    textAlign: 'center',
    fontSize: 11,
    lineHeight: 20,
  },
  footerHightlightText: {
    color: colors.link,
    textDecorationLine: 'underline',
  },
});

export const LoginScreen = Screen;
