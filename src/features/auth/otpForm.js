import {Button, Text} from 'react-native-elements';
import {
  CodeField,
  Cursor,
  useBlurOnFulfill,
  useClearByFocusCell,
} from 'react-native-confirmation-code-field';
import {
  InteractionManager,
  Keyboard,
  StatusBar,
  StyleSheet,
  View,
} from 'react-native';
import React, {useCallback, useEffect, useState} from 'react';
import {colors, globalStyles} from '@app/assets';
import {navigateRoute, resetRoute} from '@app/route';

import {CountDownText} from './component';
import {ROUTES} from '@app/constants';
import {SafeAreaView} from 'react-native-safe-area-context';
import _ from 'lodash';
import api from '@app/api';
import {iOSColors} from 'react-native-typography';
import {loginWithOTP} from '@app/store/auth/services';
import {toastAlert} from '@app/utils';
import {useAppState} from '@react-native-community/hooks';
import {useDispatch} from 'react-redux';
import {useRoute} from '@react-navigation/native';
import {useSmsUserConsent} from '@eabdullazyanov/react-native-sms-user-consent';

const Screen = () => {
  const route = useRoute();
  const [onReady, setOnReady] = useState(false);
  const [value, setValue] = useState('');
  const [props, getCellOnLayoutHandler] = useClearByFocusCell({
    value,
    setValue,
  });
  const ref = useBlurOnFulfill({value, cellCount: 6});
  const dispatch = useDispatch();
  const [isTimeOut, setTimeOut] = useState(false);
  const [isLoading, setLoading] = useState(false);
  const retrievedCode = useSmsUserConsent();
  const currentAppState = useAppState();

  useEffect(() => {
    if (retrievedCode && !_.isEmpty(retrievedCode)) {
      setValue(retrievedCode);
      onSubmit(retrievedCode);
    }
  }, [retrievedCode]);

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
      Keyboard.dismiss();
    };
  }, []);

  useEffect(() => {
    console.log('currentAppState',currentAppState)
    if (currentAppState === 'active') {
      ref?.current?.focus();
    }else{
      ref?.current?.blur();
    }
  }, [currentAppState]);

  const getOTP = useCallback(async () => {
    Keyboard.dismiss();
    if (route.params?.phone) {
      try {
        let type = '';
        const action = route.params?.action;
        switch (action) {
          case ROUTES.LOGIN_OTP:
            type = 'login';
            break;
          case ROUTES.FORGET_PASS:
            type = 'password';
            break;
          case ROUTES.REGISTER_OTP:
            type = 'register';
            break;
          default:
            break;
        }
        await api.getOTP(route.params.phone, type);
        toastAlert('Đã gửi lại mã OTP!');
        setTimeOut(false);
      } catch (error) {
        setTimeOut(false);
      }
    }
  }, []);

  const onSubmit = async code => {
    Keyboard.dismiss();
    if (code && code.length < 6) {
      toastAlert('Vui lòng nhập đủ mã OTP!');
    } else {
      setLoading(true);
      try {
        const action = route.params?.action;
        console.log('action', action);
        switch (action) {
          case ROUTES.LOGIN_OTP:
            await dispatch(loginWithOTP(route.params?.phone, code));
            setLoading(false);
            resetRoute(ROUTES.MAIN_TABS);
            break;
          case ROUTES.FORGET_PASS:
            await api.forgetPasswordOTP(route.params?.phone, code);
            setLoading(false);
            navigateRoute(ROUTES.FORGET_PASS, {
              phone: route.params?.phone,
              code: code,
            });
            break;
          case ROUTES.REGISTER_OTP:
            await api.checkOTP(route.params?.phone, code);
            setLoading(false);
            navigateRoute(ROUTES.REGISTER, {phone: route.params?.phone});
            break;
          default:
            break;
        }
      } catch (error) {
        console.log('error', error);
        setLoading(false);
      }
    }
  };

  return (
    <SafeAreaView style={styles.box}>
      <StatusBar barStyle="light-content" backgroundColor="#fff" />
      {onReady ? (
        <View style={styles.container}>
          <Text style={styles.title}>
            Chúng tôi đã gửi mã xác thực vào số điện thoại:{' '}
            <Text style={styles.titlehightLight}>{route.params?.phone}</Text>
          </Text>
          <View style={{alignItems: 'center', marginTop: 10}}>
            <CodeField
              ref={ref}
              autoFocus={true}
              value={value}
              onChangeText={txt => {
                setValue(txt);
                if (txt.length === 6) {
                  onSubmit(txt);
                }
              }}
              cellCount={6}
              rootStyle={{width: '80%', height: 200}}
              keyboardType="number-pad"
              renderCell={({index, symbol, isFocused}) => (
                <Text
                  key={index}
                  style={[
                    styles.underlineStyleBase,
                    isFocused && styles.underlineStyleHighLighted,
                  ]}
                  onLayout={getCellOnLayoutHandler(index)}>
                  {symbol || (isFocused ? <Cursor /> : null)}
                </Text>
              )}
            />
            {/* <OtpInput
              pinCount={6}
              style={{width: '80%', height: 200}}
              autoFocusOnLoad
              codeInputFieldStyle={styles.underlineStyleBase}
              codeInputHighlightStyle={styles.underlineStyleHighLighted}
              onCodeFilled={onSubmit}
            /> */}
            {!isTimeOut ? (
              <CountDownText
                title="Mã OTP sẽ hết hạn trong"
                until={120}
                onFinish={() => setTimeOut(true)}
              />
            ) : null}
            <Button
              title={'Gửi lại mã OTP'}
              type="clear"
              disabled={!isTimeOut && !isLoading}
              onPress={getOTP}
            />
          </View>
        </View>
      ) : (
        <>
          <View />
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
    margin: 10,
  },
  logo: {
    width: 200,
    marginVertical: 20,
  },
  title: {
    ...globalStyles.text,
    fontSize: 13,
  },
  titlehightLight: {
    color: colors.link,
    fontWeight: 'bold',
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
    backgroundColor: '#dc0000',
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
  underlineStyleBase: {
    ...globalStyles.text,
    width: 35,
    height: 40,
    borderWidth: 0,
    borderBottomWidth: 1,
    borderColor: colors.border,
    fontSize: 22,
    paddingVertical: 10,
    textAlign: 'center',
  },

  underlineStyleHighLighted: {
    borderColor: colors.primary,
  },
});

export const OTPFormScreen = Screen;
