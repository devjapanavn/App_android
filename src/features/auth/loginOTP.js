import api from '@app/api';
import {appDimensions, colors, globalStyles, images} from '@app/assets';
import {ImageReponsive} from '@app/components';
import React, {useCallback, useEffect, useRef, useState} from 'react';
import {Controller, useForm} from 'react-hook-form';
import {
  StyleSheet,
  InteractionManager,
  View,
  StatusBar,
  Keyboard,
} from 'react-native';
import {SafeAreaView} from 'react-native-safe-area-context';
import {Button, Icon, Input, Text} from 'react-native-elements';
import {ScrollView} from 'react-native-gesture-handler';
import {iOSColors} from 'react-native-typography';
import {gobackRoute, navigateRoute, resetRoute} from '@app/route';
import {useDispatch} from 'react-redux';
import {ROUTES} from '@app/constants';
import {useIsFocused} from '@react-navigation/native';
import {ReCapchars} from 'src/components/reCapchars';
import {toastAlert} from '@app/utils';

const Screen = props => {
  const isFocused = useIsFocused();
  const [onReady, setOnReady] = useState(false);
  const dispatch = useDispatch();
  const {
    control,
    handleSubmit,
    formState: {errors},
  } = useForm();
  const [isLoading, setLoading] = useState(false);
  const [checkRobot, setCheckRobot] = useState(true);
  const [tokenRobot, setTokenRobot] = useState('');
  const [phoneNumber, setphoneNumber] = useState('');

  const txtPhoneRef = useRef();
  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() => {
      setOnReady(true);
      setTimeout(() => {
        txtPhoneRef.current?.focus();
      }, 300);
    });
    return () => {
      interactionPromise.cancel();
    };
  }, []);
  useEffect(() => {
    if (tokenRobot) {
      SendOtpLogin();
      // navigateRoute(ROUTES.OTP_FORM, {
      //   action: ROUTES.LOGIN_OTP,
      //   phone: phoneNumber,
      // });
    }
  }, [tokenRobot]);

  const SendOtpLogin = async () => {
    setLoading(true);
    try {
      const res = await api.getOTP(phoneNumber, tokenRobot, 'login');
      navigateRoute(ROUTES.OTP_FORM, {
        action: ROUTES.LOGIN_OTP,
        phone: phoneNumber,
      });

    } catch (error) {
        console.log('error', error);
        setLoading(false);
    }
  };

  const handleCheckToken = (check, token) => {
    setCheckRobot(check);
    setTokenRobot(token);
  };

  const handlePressBack = useCallback(() => {
    gobackRoute();
  }, []);

  const handlePressLogin = useCallback(() => {
    gobackRoute();
  }, []);

  const onSubmit = async data => {
    setphoneNumber(data.username);
    Keyboard.dismiss();
    setCheckRobot(false);
    // setLoading(true);
    // setCheckRobot(true)
    // try {
    //   if(tokenRobot){
    //     const res = await api.getOTP(data.username,tokenRobot, 'login');
    //     // setTokenRobot('')
    //     // if(res.error == 200){
    //     //   toastAlert(res.message);
    //     // }else{
    //       navigateRoute(ROUTES.OTP_FORM, {
    //         action: ROUTES.LOGIN_OTP,
    //         phone: phoneNumber,
    //       });
    //     // }
    //     setLoading(false);
    //   }else{
    //     setCheckRobot(false)
    //     // setErrColor('red')
    // 		// toastAlert('Vui lòng xác nhận bạn không phải robot');
    //     setLoading(false);
    //   }
    // } catch (error) {
    //   console.log('error', error);
    //   setLoading(false);
    // }
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

            <View style={{width: '100%', paddingHorizontal: 20}}>
              <Controller
                control={control}
                rules={{
                  required: true,
                }}
                render={({field: {onChange, onBlur, value}}) => (
                  <Input
                    ref={txtPhoneRef}
                    autoCapitalize="none"
                    containerStyle={styles.inputContainer}
                    onBlur={onBlur}
                    placeholder="Số điện thoại"
                    keyboardType="phone-pad"
                    placeholderTextColor="#888"
                    inputStyle={styles.inputStyle}
                    onChangeText={onChange}
                    autoCorrect={false}
                    autoFocus
                    value={value}
                    returnKeyType="next"
                    errorMessage={
                      errors.username ? 'Vui lòng nhập số điện thoại' : null
                    }
                    onSubmitEditing={handleSubmit(onSubmit)}
                  />
                )}
                name="username"
                defaultValue=""
              />
              <ReCapchars
                checkRobot={checkRobot}
                onclickFunc={handleCheckToken}
              />
              <Button
                disabled={isLoading}
                loading={isLoading}
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
                  title={'Đăng nhập bằng tài khoản'}
                  buttonStyle={styles.loginOTPButton}
                  titleStyle={{...globalStyles.text, color: '#3b7cff'}}
                  onPress={handlePressLogin}
                />
              </View>
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
    tintColor: '#C90005',
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

export const LoginOTPScreen = Screen;
