import { Dimensions, Platform, StatusBar } from 'react-native';
import { get, getStatusBarHeight } from 'react-native-extra-dimensions-android';
const { width, height } = Dimensions.get('window');

const widthPicture = 110;
const isIphoneX =
	Platform.OS === 'ios' && Platform.isPad === false && (height > 800 || width > 800)
		? true
		: false;
const heightDevice =
	Platform.OS === 'ios' ? Dimensions.get('window').height : get('REAL_WINDOW_HEIGHT');

export const appDimensions = {
	width,
	height: heightDevice,
	heightHeader: 50,
	heightBackButton: 40,
	paddingStatusbar: 20,
	paddingTop: Platform.OS === 'android' ? 15 : isIphoneX ? 44 : 20,
	statusBarHeigh: Platform.OS === 'android' ? getStatusBarHeight() : 20,
	paddingVideoFullscreen: Platform.OS === 'android' ? 0 : isIphoneX ? 40 : 0
};

export const HEIGHT_VIDEO = w => {
	return w * 0.5625;
};
