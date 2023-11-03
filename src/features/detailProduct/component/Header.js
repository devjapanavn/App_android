import React, {useCallback, useEffect, useState} from 'react';
import {View} from 'react-native';
import {appDimensions, colors, images, spacing} from '@app/assets';
import {StyleSheet, TouchableOpacity} from 'react-native';
import MaterialIcons from 'react-native-vector-icons/MaterialIcons';
import {useSafeAreaInsets} from 'react-native-safe-area-context';
import PropTypes from 'prop-types';
import Animated, {
  BounceIn,
  Easing,
  interpolate,
  interpolateColor,
  useAnimatedReaction,
  useAnimatedStyle,
  useDerivedValue,
  useSharedValue,
  withDelay,
  withSpring,
  withTiming,
} from 'react-native-reanimated';
import {gobackRoute, navigateRoute} from '@app/route';
import {ROUTES} from '@app/constants';
import {Badge} from 'react-native-paper';
import {useSelector} from 'react-redux';
const ICONSIZE = 25;
const AnimateIcon = Animated.createAnimatedComponent(MaterialIcons);
const AnimabeBadge = Animated.createAnimatedComponent(Badge);

const HeaderComponent = props => {
  const useInsets = useSafeAreaInsets();
  const {totalCart} = useSelector(state => ({
    totalCart: state.auth.totalCart,
  }));

  const [widthSearch, setwidthSearch] = useState(42);
  const triggerCart = useSharedValue(0);
  const onLayout = useCallback(event => {
    const {width, height} = event.nativeEvent.layout;
    setwidthSearch(width);
  }, []);

  const iconContainerStyle = useAnimatedStyle(() => {
    return {
      backgroundColor: interpolateColor(
        props.offsetY.value,
        [0, appDimensions.height / 2.75],
        ['rgba(0,0,0,0.5)', 'rgba(0,0,0,0)'],
      ),
      padding: spacing.small,
      borderRadius: ICONSIZE,
    };
  });

  const iconStyle = useAnimatedStyle(() => {
    return {
      color: interpolateColor(
        props.offsetY.value,
        [0, appDimensions.height / 2.75],
        [colors.white, '#555'],
      ),
      tintColor: interpolateColor(
        props.offsetY.value,
        [0, appDimensions.height / 2.75],
        [colors.white, '#555'],
      ),
    };
  });

  useEffect(() => {
    if (props.triggerCart > 0) {
      triggerCart.value = withTiming(
        1,
        {duration: 250, easing: Easing.sin},
        isFinished => {
          if (isFinished) {
            triggerCart.value = withTiming(0, {
              duration: 250,
              easing: Easing.sin,
            });
          }
        },
      );
    }
  }, [props.triggerCart]);

  const heartbeatStyle = useAnimatedStyle(() => {
    const scale = interpolate(triggerCart.value, [0, 1], [1, 1.1]);
    return {
      transform: [{scale: scale}],
    };
  });
  return (
    <View style={[styles.container, {paddingTop: useInsets.top}]}>
      <TouchableOpacity style={styles.iconContainer} onPress={gobackRoute}>
        <Animated.View style={iconContainerStyle}>
          <AnimateIcon
            name={'chevron-left'}
            style={iconStyle}
            size={ICONSIZE}
          />
        </Animated.View>
      </TouchableOpacity>
      <View
        style={{
          flexDirection: 'row',
          justifyContent: 'flex-end',
          flex: 1,
        }}>
        <TouchableOpacity
          style={styles.iconContainer}
          onPress={() => navigateRoute(ROUTES.SEARCH)}>
          <Animated.View style={iconContainerStyle}>
            <AnimateIcon name={'search'} style={iconStyle} size={ICONSIZE} />
          </Animated.View>
        </TouchableOpacity>
        <TouchableOpacity
          style={styles.iconContainer}
          onPress={() => navigateRoute(ROUTES.MAIN_TABS)}>
          <Animated.View style={iconContainerStyle}>
            <AnimateIcon name={'home'} style={iconStyle} size={ICONSIZE} />
          </Animated.View>
        </TouchableOpacity>
        <TouchableOpacity
          style={styles.iconContainer}
          onPress={() => navigateRoute(ROUTES.CART_LIST, null, null, true)}>
          <Animated.View style={[iconContainerStyle, heartbeatStyle]}>
            <Animated.Image
              source={images.ic_cart}
              style={[iconStyle, {width: ICONSIZE, height: ICONSIZE}]}
              resizeMode="contain"
            />

            {totalCart && totalCart > 0 ? (
              <AnimabeBadge
                entering={BounceIn}
                size={25}
                style={[styles.headerBadgeCart]}>
                {totalCart ? (totalCart > 5 ? '+5' : totalCart) : 0}
              </AnimabeBadge>
            ) : null}
          </Animated.View>
        </TouchableOpacity>
      </View>
    </View>
  );
};

HeaderComponent.propTypes = {
  onPressBack: PropTypes.func,
  onPressCart: PropTypes.func,
  onLayoutCart: PropTypes.func,
};

HeaderComponent.defaultProps = {};

export const Header = React.memo(
  HeaderComponent,
  (prev, next) =>
    prev.contentOffset === next.contentOffset &&
    prev.triggerCart === next.triggerCart,
);

const styles = StyleSheet.create({
  container: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
  },
  iconContainer: {
    margin: spacing.small,
  },
  iconSearchContainer: {
    flex: 1,
  },
  icon: {
    flex: 0,
    paddingHorizontal: spacing.small,
    height: 40,
  },
  headerBadgeCart: {
    backgroundColor: colors.primary,
    position: 'absolute',
    top: -5,
    right: -5,
  },
});
