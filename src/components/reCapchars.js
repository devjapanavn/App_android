import api from '@app/api';
import { CONFIGS } from '@app/constants';
import React, {useRef, useCallback, useState, useEffect} from 'react';
import { memo } from 'react';
import {
  SafeAreaView,
  StyleSheet,
  View,
  Text,
  StatusBar,
  Button,
  TouchableOpacity,
  Image,
} from 'react-native';
import { CheckBox } from 'react-native-elements';

import Recaptcha from 'react-native-recaptcha-that-works';
const Component = ({checkRobot,onclickFunc}) => {
  const size = 'normal'; // 'invisible' | 'compact' | 'normal'
  const [key, setKey] = useState('<none>');
  const [isLoading, setLoading] = useState(false);
  

  const $recaptcha = useRef();
  useEffect(()=>{
    if(!checkRobot){
        $recaptcha.current.open();
    }
  },[checkRobot])

  const handleOpenPress = useCallback(() => {
    $recaptcha.current.open();
  }, []);

  const handleClosePress = useCallback(() => {
    $recaptcha.current.close();
  }, []);
  const verifyTokenFunc = (token) => {
    console.log(token)

    setKey(token);
    onclickFunc(true,token)
    navigates()
  }

  return (
    <>
      <SafeAreaView>
        {/* <TouchableOpacity style={{marginBottom: 10}} onPress={handleOpenPress}>
            <CheckBox 
                checked={checkRobot}
                size={24}
                title="I'm not a robot !"
                onPress={handleOpenPress}
                containerStyle={{
                    backgroundColor: 'transparent',
                    borderColor: colorCapcha,
                    borderLeftWidth:0,
                    borderRightWidth:0,
                    borderTopWidth:0,
                    paddingLeft:5
                  }}
                uncheckedColor={colorCapcha}
                textStyle={{
                    fontSize: 14,
                    // fontFamily: 'SF Pro Display',
                    // fontWeight: 'normal',
                    color: colorCapcha,
                  }}
            />
        </TouchableOpacity> */}
        <Recaptcha
          ref={$recaptcha}
        //   footerComponent={
        //     <Button title="Đóng" onPress={handleClosePress} />
        //   }
          lang="vie"
          siteKey={CONFIGS.CAPTCHA_SITE_KEY}
          baseUrl={CONFIGS.REST_API_ROOT_URL}
          size={size}
          theme="light"
          onError={err => {
            console.warn('error', err);
          }}
          onExpire={() => console.log('onExpire event')}
          onVerify={ token => verifyTokenFunc(token)}
        />
      </SafeAreaView>
    </>
  );
};
export const ReCapchars = Component;
