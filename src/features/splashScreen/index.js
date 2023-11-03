import {CONFIGS, ROUTES} from '@app/constants';
import {Linking, StatusBar, StyleSheet, View} from 'react-native';
import React, {useEffect} from 'react';
import {appDimensions, colors, globalStyles, images} from '@app/assets';
import { getProfile, getTotalCart } from '@app/store/auth/services';
import {useDispatch, useSelector} from 'react-redux';

import Alert from 'react-native/Libraries/Alert/Alert';
import FastImage from 'react-native-fast-image';
import RNBootSplash from 'react-native-bootsplash';
import Spinner from 'react-native-spinkit';
import {Text} from 'react-native-elements';
import api from '@app/api';
import {checkVersion} from 'react-native-check-version';
import { getAppInfoService } from '@app/store/root/services';
import messaging from '@react-native-firebase/messaging';
import {resetRoute} from '@app/route';
import {useSafeAreaInsets} from 'react-native-safe-area-context';

const Screen = props => {
  const insets = useSafeAreaInsets();
  const {isShowIntro, user, isLogin} = useSelector(state => ({
    isShowIntro: state.root.isShowIntro,
    user: state.auth.user,
    isLogin: state.auth.isLogin,
  }));

  const dispatch = useDispatch();
  useEffect(() => {
    StatusBar.setTranslucent(true);
    StatusBar.setBackgroundColor(colors.transparent);
    RNBootSplash.hide({fade: true});
    checkApp();
    initApp();
  }, []);

  async function checkApp() {
    const version = await checkVersion();
    if (version.needsUpdate) {
      Alert.alert(
        'Ứng dụng đã có phiên bản mới!',
        'Hiện tại ứng dụng Japana đã có phiên bản mới, Quý khách vui lòng tiến hành cập nhật ứng dụng để sử dụng phiên bản tốt nhất.',
        [
          {text: 'Đóng'},
          {
            text: 'Cập nhật',
            onPress: () => {
              Linking.openURL(version.url);
            },
          },
        ],
      );
    }
  }

  function initApp() {
    if (isLogin) {
      dispatch(getTotalCart(user?.id));
      dispatch(getProfile(user?.id));
    }
    setTimeout(() => {
      initFCMToken();
      dispatch(getAppInfoService())
        .then(res => {
          switch (res?.first_screen) {
            case 3:
              resetRoute(ROUTES.MAIN_TABS);
              break;
            case 2:
              if (!isShowIntro) {
                resetRoute(ROUTES.MAIN_TABS);
              } else {
                resetRoute(ROUTES.INTRO);
              }
              break;
            case 1:
              resetRoute(ROUTES.INTRO);
              break;
            default:
              resetRoute(ROUTES.MAIN_TABS);
              break;
          }
        })
        .catch(error => {
          Alert.alert('Lỗi!', 'Không kết nối với server. Vui lòng thử lại', [
            {text: 'Thử lại', onPress: () => initApp()},
          ]);
        });
    }, 1000);
  }

  async function initFCMToken() {
    const authorizationStatus = await messaging().requestPermission();
    if (authorizationStatus) {
      const token = await messaging().getToken();
      let userId = null;
      if (isLogin) {
        userId = user?.id;
      }
      await api.pushToken(userId, token);
    }
  }
  // function getProfile() {
  // 	dispatch(getProfileService())
  // 		.then(() => {
  // 			resetRoute(ROUTES.MAIN_TABS);
  // 		})
  // 		.catch(error => {
  // 			Alert.alert('Lỗi!', 'Không thể lấy được thông tin tài khoản', [
  // 				{ text: 'Thử lại', onPress: getProfile() }
  // 			]);
  // 		});
  // }

  return (
    <View style={[styles.container, {paddingTop: insets.top + 20}]}>
      <FastImage
        source={images.logo_vi}
        style={{width: 190, aspectRatio: 1}}
        resizeMode="contain"
      />

      <View style={styles.loadingContainer}>
        <Spinner type="FadingCircleAlt" color={colors.white} />
      </View>
      <View style={styles.footer}>
        <Text style={styles.footerText}>Công ty cổ phần Japana Việt Nam</Text>
      </View>
    </View>
  );
};
export const SplashScreen = React.memo(Screen);

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#c90005',
  },
  loadingContainer: {
    position: 'absolute',
    top: appDimensions.height / 1.5,
    width: '100%',
    justifyContent: 'center',
    alignItems: 'center',
  },
  footer: {
    position: 'absolute',
    left: 0,
    right: 0,
    bottom: 0,
  },
  footerText: {
    ...globalStyles.text,
    fontSize: 16,
    color: '#fff',
    textAlign: 'center',
    flex: 1,
    marginVertical: 50,
  },
});
