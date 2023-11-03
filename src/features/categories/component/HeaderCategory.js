import {colors, images, spacing} from '@app/assets';
import {ROUTES} from '@app/constants';
import {gobackRoute, navigateRoute} from '@app/route';
import React, {useCallback} from 'react';
import {Image, TouchableOpacity, View} from 'react-native';
import {StyleSheet} from 'react-native';
import {Badge, Header, Icon} from 'react-native-elements';
import {Searchbar} from 'react-native-paper';
import {useSelector} from 'react-redux';

const component = () => {
  const {totalCart} = useSelector(state => ({
    totalCart: state.auth.totalCart,
  }));

  const gotoCart = useCallback(() => {
    navigateRoute(ROUTES.CART_LIST, null, null, true);
  }, []);
  return (
    <>
      <Header
        statusBarProps={{backgroundColor: colors.primary}}
        backgroundColor={'#fff'}
        containerStyle={{elevation: 1}}
        style={{justifyContent: 'center'}}
        leftComponent={{
          icon: 'arrow-back-outline',
          type: 'ionicon',
          color: 'rgb(59, 72, 89)',
          onPress: gobackRoute,
        }}
        centerComponent={{
          text: 'Danh mục sản phẩm',
          style: {color: '#000', fontSize: 16},
        }}
        rightComponent={
          <TouchableOpacity activeOpacity={0.9} onPress={gotoCart}>
            <Image
              source={images.ic_cart}
              style={{width: 25, height: 25, tintColor: 'rgb(59, 72, 89)'}}
              resizeMode="contain"
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
        }
      />
      <TouchableOpacity
        activeOpacity={0.8}
        onPress={() => navigateRoute(ROUTES.SEARCH)}>
        <View pointerEvents="none">
          <Searchbar
            placeholder="Tìm kiếm sản phẩm ..."
            style={{margin: 10, height: 44, elevation: 1}}
          />
        </View>
      </TouchableOpacity>
    </>
  );
};
export const HeaderCategory = React.memo(component, () => true);

const styles = StyleSheet.create({
  headerBadgeCart: {
    position: 'absolute',
    top: -5,
    right: -5,
  },
});
