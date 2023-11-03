import {colors, images, spacing} from '@app/assets';
import {ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import React, {useCallback, useEffect} from 'react';
import {
  Image,
  StatusBar,
  StyleSheet,
  TouchableOpacity,
  View,
} from 'react-native';
import {Badge, Icon} from 'react-native-elements';
import { useSelector } from 'react-redux';

const component = ({onPressMenu, onPressCart}) => {

  const {totalCart} = useSelector(state => ({
    totalCart: state.auth.totalCart,
  }));

  useEffect(() => {
    StatusBar.setBackgroundColor(colors.primary);
    StatusBar.setBarStyle('light-content');
  }, []);

  const gotoCart = useCallback(() => {
    navigateRoute(ROUTES.CART_LIST, null, null, true);
  }, []);
  return (
    <View style={styles.container}>
      <View style={styles.headerContainer}>
        <Icon
          name="ios-grid-outline"
          type="ionicon"
          color={colors.white}
          size={25}
          onPress={onPressMenu}
          activeOpacity={0.9}
          containerStyle={styles.headerAction}
        />
        <Image
          source={images.ic_logo}
          style={styles.headerImage}
          resizeMode="contain"
        />
        <TouchableOpacity activeOpacity={0.9} style={styles.headerAction}    onPress={gotoCart}>
          <Icon
            name="shopping-basket"
            type="font-awesome-5"
            color={colors.white}
            size={25}
         
          />
          {totalCart && totalCart > 0 ? (
              <Badge
                value={totalCart ? (totalCart > 5 ? '+5' : totalCart) : 0}
                badgeStyle={{width: 25}}
                status="warning"
                containerStyle={styles.headerBadgeCart}
              />
            ) : null}
        </TouchableOpacity>
      </View>
    </View>
  );
};
export const Header = React.memo(component, () => true);

const styles = StyleSheet.create({
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
});
