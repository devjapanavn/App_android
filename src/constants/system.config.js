import Config from 'react-native-config';
import {Platform} from 'react-native';
export const CONFIGS = {
  IS_DEBUG: Config.IS_DEBUG,

  REST_API_ROOT_URL:
    process.env.NODE_ENV === 'development'
      ? 'http://testapi.japana.vn/api/'
      : 'http://api.japana.vn/api/',
  // REST_API_ROOT_URL: 'http://api.japana.vn/api/',
  IMAGE_URL: Config.IMAGE_URL,
  VIDEO_URL: Config.VIDEO_URL,
  TOKEN_USERNAME: Config.TOKEN_USERNAME,
  TOKEN_PASSWORD: Config.TOKEN_PASSWORD,

  API_VERSION: Config.API_VERSION,
  DEVICE_TYPE: Platform.OS === 'android' ? 'android' : 'ios',
  TIMEOUT: 30000,
  SHARE_URL: 'https://play.google.com/store/apps/details?id=',
  URI_PREFIX: '',
  PRIMARY_COLOR: Config.PRIMARY_COLOR,
  DARK_PRIMARY_COLOR: Config.DARK_PRIMARY_COLOR,
  CAPTCHA_SITE_KEY: '6LdJJJooAAAAAI0wq3v5a1sGQdS2ztj-9660C16B',
  CAPTCHA_SECRET_KEY: '6LdJJJooAAAAAC_00ayU_icOIRoR3lhQQSwetO24',
};
