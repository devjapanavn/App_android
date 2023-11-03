import api from '@app/api';
import { appDimensions, colors, globalStyles, images } from '@app/assets';
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
import { Button, Icon, Input, Text } from 'react-native-elements';
import { ScrollView } from 'react-native-gesture-handler';
import { iOSColors } from 'react-native-typography';
import { gobackRoute, navigateRoute, resetRoute } from '@app/route';
import { useDispatch } from 'react-redux';
import { ROUTES } from '@app/constants';
import { useIsFocused, useRoute } from '@react-navigation/native';
import { toastAlert } from '@app/utils';

const Screen = props => {
  const isFocused = useIsFocused();
  const route = useRoute()
  const [onReady, setOnReady] = useState(false);
  const [isShowPass, setShowPass] = useState(false);
  const [isShowConfirmPass, setShowConfirmPass] = useState(false);
  const dispatch = useDispatch();

  const passwordConfirmRef = useRef(null);
  const {
    control,
    handleSubmit,
    formState: { errors },
  } = useForm();
  const [isLoading, setLoading] = useState(false);

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  const handlePressBack = useCallback(() => {
    gobackRoute();
  }, []);


  const handleShowPass = useCallback(() => {
    setShowPass(val => !val);
  }, [isShowPass]);

  const onSubmit = async data => {
    Keyboard.dismiss();
    setLoading(true);
    try {
      const res = await api.resetPassword({
        otp_sms: route.params?.code,
        phone: route.params?.phone,
        password_new: data.password,
        repassword_new: data.passwordConfirm
      });
      setLoading(false);
      navigateRoute(ROUTES.LOGIN);
      toastAlert('Đã khôi phục mật khẩu thành công!')
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
                      onPress: () => setShowPass(prev => !prev),
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
                title={'Đặt lại mật khẩu'}
                buttonStyle={styles.loginButton}
                onPress={handleSubmit(onSubmit)}
              />
            </View>
            <View style={styles.footer}></View>
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

export const ForgotPasswordScreen = Screen;
