import {
  Image,
  StatusBar,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
  useWindowDimensions,
} from 'react-native';
import React, {useEffect, useRef} from 'react';
import {appDimensions, colors, globalStyles, images} from '@app/assets';
import {useDispatch, useSelector} from 'react-redux';

import AppIntroSlider from 'react-native-app-intro-slider';
import FastImage from 'react-native-fast-image';
import {ROUTES} from '@app/constants';
import _ from 'lodash';
import { hideIntro } from '@app/store/root/reducers';
import {resetRoute} from '@app/route';
import {useSafeAreaInsets} from 'react-native-safe-area-context';

const slides = [
  {
    key: '1',
    title: '',
    text: '',
    image: images.logo_vi,
    backgroundColor: '#c90005',
    type: 1,
  },
  {
    key: '2',
    title: '',
    text: '',
    image: images.intro_1,
    backgroundColor: '#448AFF',
    type: 2,
  },
  {
    key: '3',
    title: '',
    text: '',
    image: images.intro_2,
    backgroundColor: '#4CAF50',
    type: 2,
  },
];

const Screen = props => {
  const insets = useSafeAreaInsets();
  const {width, height} = useWindowDimensions();
  const dispatch = useDispatch();
  const sliderRef = useRef(null);
  const {first_image_array} = useSelector(state => ({
    first_image_array: state.root.first_image_array,
  }));

  useEffect(() => {
    dispatch(hideIntro());
  }, []);

  function _onDone() {
    resetRoute(ROUTES.MAIN_TABS);
  }
  const _renderItem = ({item, index}) => {
    return (
      <View style={[styles.slide]}>
        <TouchableOpacity
          onPress={() => _onDone()}
          style={[
            styles.skipContainer,
            {top: insets.top + 10, right: insets.right + 10, padding: 10},
          ]}>
          <Text style={styles.skipButton}>Bỏ qua</Text>
        </TouchableOpacity>
        <Image
          source={{uri: item[`image_${index + 1}`]}}
          style={{
            width: width,
            height: height,
            backgroundColor: item.backgroundColor,
          }}
          resizeMode={'contain'}
        />
      </View>
    );
  };

  const _keyExtractor = item => item.key;

  return (
    <View style={styles.container}>
      <StatusBar translucent backgroundColor="transparent" />
      <AppIntroSlider
        ref={sliderRef}
        keyExtractor={_keyExtractor}
        renderItem={_renderItem}
        activeDotStyle={[styles.dot, {backgroundColor: 'white'}]}
        dotStyle={[styles.dot, {backgroundColor: 'rgba(0, 0, 0, .2)'}]}
        data={first_image_array || []}
        onDone={_onDone}
        doneLabel="Đóng"
        nextLabel="Tiếp"
      />
    </View>
  );
};
export const IntroScreen = React.memo(Screen);

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  slide: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  image: {
    width: '100%',
    height: '100%',
  },
  text: {
    color: 'rgba(255, 255, 255, 0.8)',
    textAlign: 'center',
  },
  title: {
    fontSize: 22,
    color: 'white',
    textAlign: 'center',
  },
  paginationContainer: {
    position: 'absolute',
    bottom: 16,
    left: 16,
    right: 16,
  },
  paginationDots: {
    height: 16,
    margin: 16,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  dot: {
    width: 30,
    height: 10,
    borderRadius: 5,
    marginHorizontal: 4,
  },
  buttonContainer: {
    flexDirection: 'row',
    marginHorizontal: 24,
  },
  button: {
    flex: 1,
    paddingVertical: 20,
    marginHorizontal: 8,
    borderRadius: 24,
    backgroundColor: '#1cb278',
  },
  buttonText: {
    color: 'white',
    fontWeight: '600',
    textAlign: 'center',
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
  skipContainer: {
    position: 'absolute',
    zIndex: 20,
  },
  skipButton: {
    ...globalStyles.text,
    color: colors.white,
    fontSize: 18,
  },
});
