import {colors, spacing} from '@app/assets';
import {ROUTES} from '@app/constants';
import {gobackRoute, navigateRoute} from '@app/route';
import React, {useCallback, useEffect} from 'react';
import {
  StatusBar,
  StyleSheet,
  TouchableOpacity,
  TouchableWithoutFeedback,
  View,
} from 'react-native';
import {Badge, Icon, Text} from 'react-native-elements';

const component = ({onPressMenu, onPressCart, keyword}) => {
  useEffect(() => {
    StatusBar.setBackgroundColor(colors.primary);
    StatusBar.setBarStyle('light-content');
  }, []);

  const gotoCart = useCallback(() => {
    navigateRoute(ROUTES.CART_LIST, null, null, true);
  }, []);
  const onBack = useCallback(() => {
    gobackRoute();
  }, []);
  return (
    <View style={styles.container}>
      <View style={styles.headerContainer}>
        <Icon
          name="arrow-back-outline"
          type="ionicon"
          color={colors.white}
          size={25}
          onPress={onBack}
          activeOpacity={0.9}
          containerStyle={styles.headerAction}
        />
        <TouchableWithoutFeedback onPress={onBack}>
          <View style={styles.searchContainer}>
            <Icon name="search" type="ionicon" color={colors.black} />
            <Text style={styles.searchTitle}>{keyword}</Text>
          </View>
        </TouchableWithoutFeedback>
        <TouchableOpacity
          activeOpacity={0.9}
          style={styles.headerAction}
          onPress={gotoCart}>
          <Icon
            name="shopping-basket"
            type="font-awesome-5"
            color={colors.white}
            size={25}
          />
          <Badge
            value={'+5'}
            status="warning"
            containerStyle={styles.headerBadgeCart}
          />
        </TouchableOpacity>
      </View>
    </View>
  );
};
export const HeaderSearch = React.memo(component, () => true);

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
  searchContainer: {
    flex: 1,
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
    color: colors.black,
  },
});
