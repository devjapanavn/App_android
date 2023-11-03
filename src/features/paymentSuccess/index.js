import Animated, {
  useAnimatedScrollHandler,
  useSharedValue,
} from 'react-native-reanimated';
import {
  Avatar,
  Button,
  Icon,
  ListItem,
  Text,
  Tooltip,
} from 'react-native-elements';
import {CONTACT_TYPE, ROUTES} from '@app/constants';
import {FooterTab, HeaderComponent} from '@app/components';
import {
  Image,
  Linking,
  StatusBar,
  StyleSheet,
  TouchableOpacity,
  View,
} from 'react-native';
import React, {useEffect} from 'react';
import {colors, globalStyles, images} from '@app/assets';
import {resetAndNavigateRoute, resetRoute} from '@app/route';
import {useDispatch, useSelector} from 'react-redux';

import Clipboard from '@react-native-clipboard/clipboard';
import {SafeAreaView} from 'react-native-safe-area-context';
import Spinner from 'react-native-spinkit';
import api from '@app/api';
import {getTotalCart} from '@app/store/auth/services';
import {toastAlert} from '@app/utils';
import {useQuery} from 'react-query';

const Screen = () => {
  const {user, bank, isLogin, hotline, social} = useSelector(state => ({
    user: state.auth.user,
    bank: state.root.bank,
    isLogin: state.auth.isLogin,
    hotline: state.root.hotline,
    social: state.root.social,
    share: state.root.share,
  }));
  const contentOffset = useSharedValue(0);
  const handleScroll = useAnimatedScrollHandler(event => {
    contentOffset.value = event.contentOffset.y;
  });
  const dispatch = useDispatch();
  useEffect(() => {
    dispatch(getTotalCart(user?.id));
  }, []);

  const fetch = async () => {
    return await api.paymentThanks(user?.id);
  };

  const {data, isLoading} = useQuery(['getThankPayment'], fetch);

  async function coppy(val) {
    await Clipboard.setString(val);
    toastAlert('Đã sao chép số tài khoản: ' + val);
  }

  function openPopover(type) {
    switch (type) {
      case CONTACT_TYPE.HOTLINE:
        Linking.openURL('tel:' + hotline);
        break;
      case CONTACT_TYPE.ZALO:
        Linking.openURL(social?.zalo);
        break;
      case CONTACT_TYPE.MESSENGER:
        Linking.openURL(social?.messenger);
        break;
      default:
        Linking.openURL('tel:' + hotline);
        break;
    }
  }

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar
        barStyle="light-content"
        backgroundColor={colors.darkPrimary}
      />
      <HeaderComponent
        offsetY={contentOffset}
        onPressCart={() =>
          resetAndNavigateRoute([
            {name: ROUTES.MAIN_TABS},
            {name: ROUTES.CART_LIST},
          ])
        }
        onPressCategory={() =>
          resetAndNavigateRoute([
            {name: ROUTES.MAIN_TABS},
            {name: ROUTES.CATEGORIES},
          ])
        }
        onPressSearch={() =>
          resetAndNavigateRoute([
            {name: ROUTES.MAIN_TABS},
            {name: ROUTES.SEARCH},
          ])
        }
      />

      <Animated.ScrollView
        style={{flex: 1}}
        showsVerticalScrollIndicator={false}
        onScroll={handleScroll}>
        <View style={styles.bodyContainer}>
          <View style={{alignItems: 'center'}}>
            <Image source={images.shipping} style={styles.imageShip} />
          </View>

          {isLoading ? (
            <View style={styles.box}>
              <View
                style={{
                  flex: 1,
                  justifyContent: 'center',
                  alignItems: 'center',
                }}>
                <Spinner type="Circle" color={colors.primary} size={25} />
              </View>
            </View>
          ) : (
            <>
              <View style={styles.boxheader}>
                <Text
                  style={[styles.title, {color: '#F44336', fontWeight: '600'}]}>
                  {data?.thank}
                </Text>
              </View>

              <View style={styles.box}>
                <Text style={styles.desciption}>{data?.description}</Text>
              </View>
              {bank ? (
                <View style={styles.boxPayment}>
                  <Text style={styles.desciption}>{bank.desc}</Text>
                  <View style={styles.row}>
                    <Text style={styles.bankTitle}>Chủ TK: </Text>
                    <View>
                      <Text>{bank.account}</Text>
                    </View>
                  </View>
                  <View style={styles.row}>
                    <Text style={styles.bankTitle}>Số TK: </Text>
                    <View
                      style={[
                        globalStyles.row,
                        {
                          justifyContent: 'space-between',
                          flex: 1,
                          alignItems: 'center',
                        },
                      ]}>
                      <Text>{bank.number_account}</Text>
                      <Button
                        title={'Sao chép'}
                        buttonStyle={{paddingVertical: 4, margin: 0}}
                        titleStyle={{fontSize: 12}}
                        onPress={() => coppy(bank.number_account)}
                      />
                    </View>
                  </View>
                  <View style={styles.row}>
                    <Text style={styles.bankTitle}>Chi nhánh: </Text>
                    <View>
                      <Text>{bank.bank}</Text>
                    </View>
                  </View>
                </View>
              ) : null}
            </>
          )}
          <View style={styles.footer}>
            <Button
              title="Tiếp tục mua sắm"
              type="outline"
              onPress={() => resetRoute(ROUTES.MAIN_TABS)}
              titleStyle={{
                color: colors.primary,
                fontSize: 15,
                padding: 10,
              }}
              buttonStyle={{borderColor: colors.primary, marginVertical: 4}}
            />
            <Button
              title="Theo dõi đơn hàng"
              titleStyle={{
                color: colors.white,
                fontSize: 15,
                padding: 10,
              }}
              buttonStyle={{backgroundColor: colors.primary, marginVertical: 4}}
              onPress={() =>
                resetAndNavigateRoute([
                  {name: ROUTES.MAIN_TABS},
                  {name: ROUTES.ORDER_LIST},
                ])
              }
            />
          </View>
        </View>
      </Animated.ScrollView>
      <FooterTab />
    </SafeAreaView>
  );
};

export const PaymentSuccessScreen = Screen;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.white,
  },
  imageShip: {
    height: 200,
    aspectRatio: 1,
    resizeMode: 'contain',
  },
  bodyContainer: {
    paddingHorizontal: 10,
  },
  boxheader: {
    backgroundColor: '#FFEB3B',
    marginBottom: 4,
  },
  box: {
    minHeight: 60,
    backgroundColor: '#f0f6ff',
    marginBottom: 3,
  },
  boxPayment: {
    backgroundColor: '#F0F4C3',
    borderWidth: 1,
    borderRadius: 5,
    borderColor: '#4CAF50',
  },
  title: {
    margin: 10,
    color: '#0F83FF',
    fontSize: 14,
    lineHeight: 22,
    textAlign: 'center',
  },
  desciption: {
    color: '#2a2a2a',
    fontSize: 14,
    lineHeight: 22,
    padding: 10,
  },
  phone: {
    color: colors.primary,
    fontWeight: '500',
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 10,
  },
  row: {
    flexDirection: 'row',
    paddingHorizontal: 10,
    paddingVertical: 5,
    alignItems: 'center',
  },
  bankTitle: {
    ...globalStyles.text,
    width: 80,
  },
  footerButtonContainer: {
    flex: 1,
  },
  footerButton: {
    backgroundColor: '#fff',
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 8,
    backgroundColor: '#fff',
    borderTopColor: 'rgba(0, 0, 0, 0.2)',
    borderTopWidth: 0.5,
  },
  footerButtonTitle: {
    ...globalStyles.text,
    color: '#888',
    fontSize: 13,
  },
  popoverText: {
    fontSize: 15,
    fontFamily: 'SF Pro Display',
    color: '#000',
  },
});
