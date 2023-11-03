import { colors, images, spacing } from '@app/assets';
import { ROUTES } from '@app/constants';
import { navigateRoute, resetRoute } from '@app/route';
import React, { useCallback, useEffect } from 'react';
import {
  Image,
  StatusBar,
  StyleSheet,
  TouchableOpacity,
  TouchableWithoutFeedback,
  View,
} from 'react-native';
import { Badge, Icon, Text } from 'react-native-elements';
import Animated, {
  interpolate,
  useAnimatedStyle,
  Extrapolate,
} from 'react-native-reanimated';
import { useSelector } from 'react-redux';

const component = ({ offsetY }) => {
  useEffect(() => {
    StatusBar.setBackgroundColor(colors.primary);
    StatusBar.setBarStyle('light-content');
  }, []);
  const { totalCart } = useSelector(state => ({
    totalCart: state.auth.totalCart,
  }));

  const handleGoToSearch = useCallback(() => {
    navigateRoute(ROUTES.SEARCH);
  }, []);
  const gotoCart = useCallback(() => {
    navigateRoute(ROUTES.CART_LIST, null, null, true);
  }, []);
  const gotoCategory = useCallback(() => {
    navigateRoute(ROUTES.CATEGORIES);
  }, []);

  const imageStyles = useAnimatedStyle(() => {
    const translateY = interpolate(offsetY.value, [0, 45], [0, -45]);
    const scale = interpolate(offsetY.value, [0, 100], [1, 0]);
    const opacity = interpolate(offsetY.value, [0, 100], [1, 0]);
    return {
      transform: [{ translateY }, { scale }],
      opacity,
    };
  });

  const searchStyle = useAnimatedStyle(() => {
    const translateY = interpolate(
      offsetY.value,
      [0, 100],
      [50, 10],
      Extrapolate.CLAMP,
    );
    const scale = interpolate(
      offsetY.value,
      [0, 100],
      [0, 40],
      Extrapolate.CLAMP,
    );
    return {
      transform: [{ translateY }],
      position: 'absolute',
      left: scale,
      right: scale,
    };
  });
  const containerAnimated = useAnimatedStyle(() => {
    const height = interpolate(
      offsetY.value,
      [0, 100],
      [100, 50],
      Extrapolate.CLAMP,
    );

    return {
      height: height,
    };
  });

  return (
    <>
      <Animated.View
        style={[styles.containerHeader, containerAnimated]}
        onLayout={e => {
          console.log(e.nativeEvent.layout);
        }}>
        <View style={styles.headerContainer}>
          <Icon
            name="ios-grid-outline"
            type="ionicon"
            color={colors.white}
            size={25}
            onPress={gotoCategory}
            activeOpacity={0.9}
            containerStyle={styles.headerAction}
          />
          <TouchableOpacity onPress={() => resetRoute(ROUTES.MAIN_TABS)} activeOpacity={0.8}>
            <Animated.Image
              source={images.ic_logo}
              style={[styles.headerImage, imageStyles]}
              resizeMode="contain"
            />
          </TouchableOpacity>


          <TouchableOpacity
            activeOpacity={0.9}
            style={styles.headerAction}
            onPress={gotoCart}>
            <Image
              source={images.ic_cart}
              style={{ width: 25, height: 25, tintColor: '#fff' }}
              resizeMode="contain"
            />
            {totalCart && totalCart > 0 ? (
              <Badge
                value={totalCart ? (totalCart > 5 ? '+5' : totalCart) : 0}
                badgeStyle={{ width: 25 }}
                status="warning"
                containerStyle={styles.headerBadgeCart}
              />
            ) : null}
          </TouchableOpacity>
        </View>
        <Animated.View style={[searchStyle]}>
          <TouchableWithoutFeedback onPress={handleGoToSearch}>
            <View style={styles.searchContainer}>
              <Icon
                name="search"
                type="ionicon"
                color={colors.black}
                size={20}
              />
              <Text style={styles.searchTitle}>Tìm kiếm sản phẩm</Text>
            </View>
          </TouchableWithoutFeedback>
        </Animated.View>
      </Animated.View>
    </>
  );
};
export const HeaderHome = React.memo(
  component,
  (prev, next) => prev.offsetY === next.offsetY,
);

const styles = StyleSheet.create({
  containerHeader: {
    backgroundColor: colors.primary,
  },
  container: {
    backgroundColor: colors.primary,
  },
  headerContainer: {
    flexDirection: 'row',
    height: 50,
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  headerImage: {
    height: 40,
    width: 120,
  },
  headerAction: {
    padding: spacing.medium,
  },
  headerBadgeCart: {
    position: 'absolute',
    top: 0,
    right: 0,
  },
  searchContainer: {
    height: 40,
    backgroundColor: 'white',
    flexDirection: 'row',
    alignItems: 'center',
    marginHorizontal: spacing.medium,
    padding: spacing.small,
    borderRadius: 4,
  },
  searchTitle: {
    marginLeft: spacing.small,
    color: colors.gray,
  },
});
