import React, { useEffect } from 'react';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { StatusBar } from 'react-native';
import { ROUTES } from '@app/constants';
import { SplashScreen } from './splashScreen';
import { MainTabsScreen } from './mainTabs';
import messaging from '@react-native-firebase/messaging';
import { showMessage } from 'react-native-flash-message';
import { colors } from '@app/assets';
import { IntroScreen } from './Intro';
import { DetailProductScreen } from './detailProduct';
import { AllCommentsScreen } from './allComments';
import { Icon, withBadge } from 'react-native-elements';
import { WriteCommentScreen } from './writeComment';
import { CartListScreen } from './cartList';
import { PaymentScreen } from './payment';
import { PaymentSuccessScreen } from './paymentSuccess';
import { CategoriesScreen } from './categories';
import { SearchAllScreen } from './searchAll';
import { SearchScreen } from './search';
import { CategoryDetailScreen } from './categoryDetail';
import { AddressListScreen } from './addressList';
import { gobackRoute } from '@app/route';
import { AddressAddScreen } from './addressAdd';
import { ProfileScreen } from './profile';
import { OrderListScreen } from './orderList';
import { OrderDetailScreen } from './orderDetail';
import { NewsDetailScreen } from './newsDetail';
import { LoginScreen } from './auth/login';
import { OTPFormScreen } from './auth/otpForm';
import { LoginOTPScreen } from './auth/loginOTP';
import { RegisterOTPScreen } from './auth/registerOTP';
import { RegisterScreen } from './auth/register';
import { BlockPageScreen } from './blockPage';
import { ForgotPasswordOTPScreen } from './auth/forgotPasswordOTP';
import { ChangePassScreen } from './changePassword';
import { StaticBlog } from './staticBlog';
import { ListStaticBlog } from './staticBlogList';
import { NotificationDetailScreen } from './notificationDetail';
import { ForgotPasswordScreen } from './auth/forgotPassword';
import { VoucherListScreen } from './voucherList';
import { VoucherDetailsScreen } from './voucherDetails';
import { ListSaleAutoScreen } from './listSaleAuto';
import { ImageListScreen } from './imageList';

const Stack = createNativeStackNavigator();
const BadgedIcon = withBadge(99)(Icon);

export default App = () => {
  useEffect(() => {
    const unsubscribe = messaging().onMessage(async remoteMessage => {
      if (remoteMessage && remoteMessage.notification) {
        showMessage({
          message: remoteMessage.notification?.title,
          description: remoteMessage.notification?.body,
          type: 'info',
          backgroundColor: '#FFDFAC',
          color: colors.black,
          statusBarHeight: StatusBar.currentHeight,
          onPress: () => proccessNoti(remoteMessage.data),
        });
      }
    });
    return unsubscribe;
  }, []);

  function proccessNoti(data = { id, room, type }) {
    if (data && data.type) {
      switch (data.type) {
        default:
          break;
      }
    }
  }

  return (
    <Stack.Navigator>
      <Stack.Group>
        <Stack.Screen
          name={ROUTES.SPLASHSCREEN}
          component={SplashScreen}
          options={{ headerShown: false }}
        />
        <Stack.Screen
          name={ROUTES.INTRO}
          component={IntroScreen}
          options={{ headerShown: false }}
        />

        <Stack.Screen
          name={ROUTES.MAIN_TABS}
          component={MainTabsScreen}
          options={{ headerShown: false }}
        />
        <Stack.Screen
          name={ROUTES.DETAIL_PRODUCT}
          component={DetailProductScreen}
          options={{ headerShown: false }}
        />
        <Stack.Screen
          name={ROUTES.CATEGORIES}
          component={CategoriesScreen}
          options={{ headerShown: false }}
        />
        <Stack.Screen
          name={ROUTES.SEARCH}
          component={SearchScreen}
          options={{ headerShown: false }}
        />
        <Stack.Screen
          name={ROUTES.SEARCH_ALL}
          component={SearchAllScreen}
          options={{ headerShown: false }}
        />
        <Stack.Screen
          name={ROUTES.CATEGORY_DETAIL}
          component={CategoryDetailScreen}
          options={{ headerShown: false }}
        />
        <Stack.Screen
          name={ROUTES.NEWS_DETAIL}
          component={NewsDetailScreen}
          options={{ headerShown: false }}
        />
        <Stack.Screen
          name={ROUTES.BLOG_PAGE}
          component={BlockPageScreen}
          options={{ headerShown: false }}
        />
      </Stack.Group>

      <Stack.Group>
        <Stack.Screen
          name={ROUTES.LOGIN}
          component={LoginScreen}
          options={{ headerShown: false }}
        />
        <Stack.Screen
          name={ROUTES.LOGIN_OTP}
          component={LoginOTPScreen}
          options={{ headerShown: false }}
        />
        <Stack.Screen
          name={ROUTES.FORGET_PASS_OTP}
          component={ForgotPasswordOTPScreen}
          options={{ headerShown: false }}
        />
        <Stack.Screen
          name={ROUTES.FORGET_PASS}
          component={ForgotPasswordScreen}
          options={{ headerShown: false }}
        />
        <Stack.Screen
          name={ROUTES.OTP_FORM}
          component={OTPFormScreen}
          options={{
            title: 'Nhập mã OTP',
            headerTitleAlign: 'center',
            headerStyle: { backgroundColor: '#fff' },
          }}
        />
        <Stack.Screen
          name={ROUTES.REGISTER}
          component={RegisterScreen}
          options={{
            headerTitle: 'Đăng ký thành viên',
            headerTitleAlign: 'center',
            title: "Đăng ký",
            headerStyle: { backgroundColor: '#fff' },
            headerLeft: props => (
              <Icon
                name={'chevron-back-outline'}
                type="ionicon"
                color="#888"
                {...props}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.REGISTER_OTP}

          component={RegisterOTPScreen}
          options={{ headerShown: false }}
        />
      </Stack.Group>

      <Stack.Group>
        <Stack.Screen
          name={ROUTES.ALL_COMMENT}
          component={AllCommentsScreen}
          options={{
            presentation: 'modal',
            title: 'Đánh giá sản phẩm',
            headerTitleAlign: 'center',
            headerStyle: { backgroundColor: '#fff' },
            headerLeft: props => (
              <Icon
                name={'close'}
                type="ionicon"
                color="#888"
                {...props}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.WRITE_COMMENT}
          component={WriteCommentScreen}
          options={{
            presentation: 'modal',
            title: 'Viết đánh giá',
            headerTitleAlign: 'center',
            headerStyle: { backgroundColor: '#fff' },
            headerLeft: props => (
              <Icon
                name={'close'}
                type="ionicon"
                color="#888"
                {...props}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.CART_LIST}
          component={CartListScreen}
          options={{
            presentation: 'modal',
            title: 'Giỏ hàng',
            headerTitleAlign: 'center',
            headerStyle: { backgroundColor: '#fff' },
            headerLeft: props => (
              <Icon
                name={'arrow-back-outline'}
                type="ionicon"
                color="#888"
                {...props}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.PAYMENT}
          component={PaymentScreen}
          options={{
            presentation: 'modal',
            title: 'Thanh toán',
            headerTitleAlign: 'center',
            headerStyle: { backgroundColor: '#fff' },
            headerLeft: props => (
              <Icon
                name={'arrow-back-outline'}
                type="ionicon"
                color="#888"
                {...props}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.ADDRESS_LIST}
          component={AddressListScreen}
          options={{
            presentation: 'fullScreenModal',
            title: 'Sổ địa chỉ',
            headerTitleStyle: { color: '#fff' },
            headerStyle: { backgroundColor: '#dc0000' },
            headerBackVisible: false,
            headerRight: props => (
              <Icon
                name={'cancel'}
                type="material"
                color="#fff"
                {...props}
                containerStyle={{ paddingRight: 10 }}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.VOUCHER_LIST}
          component={VoucherListScreen}
          options={{
            presentation: 'fullScreenModal',
            title: 'Danh sách mã voucher',
            headerTitleStyle: { color: '#fff' },
            headerStyle: { backgroundColor: '#dc0000' },
            headerBackVisible: false,
            headerRight: props => (
              <Icon
                name={'cancel'}
                type="material"
                color="#fff"
                {...props}
                // containerStyle={{ paddingRight: 10 }}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.IMAGES_LIST}
          component={ImageListScreen}
          options={{
            presentation: 'fullScreenModal',
            title: '',
            // headerTitleStyle: { color: '#fff' },
            headerStyle: { backgroundColor: '#000' },
            // headerBackVisible: false,
            headerRight: props => (
              <Icon
                name={'cancel'}
                type="material"
                color="#fff"
                {...props}
                // containerStyle={{ paddingRight: 10 }}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.VOUCHER_DETAILS}
          component={VoucherDetailsScreen}
          options={{
            presentation: 'fullScreenModal',
            title: 'Chi tiết voucher',
            headerTitleStyle: { color: '#fff' },
            headerStyle: { backgroundColor: '#dc0000' },
            headerBackVisible: false,
            headerRight: props => (
              <Icon
                name={'cancel'}
                type="material"
                color="#fff"
                {...props}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.SALE_AUTO}
          component={ListSaleAutoScreen}
          options={{
            presentation: 'fullScreenModal',
            title: 'Chọn quà tặng',
            headerTitleStyle: { color: '#fff' },
            headerStyle: { backgroundColor: '#dc0000' },
            headerBackVisible: false,
            headerRight: props => (
              <Icon
                name={'cancel'}
                type="material"
                color="#fff"
                {...props}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.PROFILE}
          component={ProfileScreen}
          options={{
            presentation: 'modal',
            headerTitle: 'Tài khoản',
            headerTitleStyle: { color: '#fff' },
            headerStyle: { backgroundColor: '#dc0000' },
            headerBackVisible: false,
            headerRight: props => (
              <Icon
                name={'cancel'}
                type="material"
                color="#fff"
                {...props}
                containerStyle={{ paddingRight: 10 }}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.CHANGE_PASSWORD}
          component={ChangePassScreen}
          options={{
            presentation: 'modal',
            headerTitle: 'Đổi mật khẩu',
            headerTitleStyle: { color: '#fff' },
            headerStyle: { backgroundColor: '#dc0000' },
            headerBackVisible: false,
            headerRight: props => (
              <Icon
                name={'cancel'}
                type="material"
                color="#fff"
                {...props}
                containerStyle={{ paddingRight: 10 }}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.STATIC_BLOG}
          component={StaticBlog}
          options={{
            presentation: 'card',
            title: ' ',
            headerTitleStyle: { color: '#fff' },
            headerStyle: { backgroundColor: '#dc0000' },
            headerBackVisible: false,
            headerRight: props => (
              <Icon
                name={'cancel'}
                type="material"
                color="#fff"
                {...props}
                containerStyle={{ paddingRight: 10 }}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.NOTIFICATION_DETAIL}
          component={NotificationDetailScreen}
          options={{
            presentation: 'card',
            title: ' ',
            headerTitleStyle: { color: '#fff' },
            headerStyle: { backgroundColor: '#dc0000' },
            headerBackVisible: false,
            headerRight: props => (
              <Icon
                name={'cancel'}
                type="material"
                color="#fff"
                {...props}
                containerStyle={{ paddingRight: 10 }}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.LIST_STATIC_BLOG}
          component={ListStaticBlog}
          options={{
            presentation: 'card',
            title: 'Điều khoản và chính sách',
            headerTitleStyle: { color: '#fff' },
            headerStyle: { backgroundColor: '#dc0000' },
            headerBackVisible: false,
            headerRight: props => (
              <Icon
                name={'cancel'}
                type="material"
                color="#fff"
                {...props}
                containerStyle={{ paddingRight: 10 }}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.ORDER_LIST}
          component={OrderListScreen}
          options={{
            presentation: 'modal',
            headerTitle: 'Lịch sử đặt hàng',
            headerTitleStyle: { color: '#fff' },
            headerStyle: { backgroundColor: '#dc0000' },
            headerBackVisible: false,
            headerRight: props => (
              <Icon
                name={'cancel'}
                type="material"
                color="#fff"
                {...props}
                containerStyle={{ paddingRight: 10 }}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.ORDER_DETAIL}
          component={OrderDetailScreen}
          options={{
            presentation: 'modal',
            headerTitle: 'Chi tiết đơn hàng',
            headerTitleStyle: { color: '#fff' },
            headerStyle: { backgroundColor: '#dc0000' },
            headerBackVisible: false,
            headerRight: props => (
              <Icon
                name={'cancel'}
                type="material"
                color="#fff"
                {...props}
                containerStyle={{ paddingRight: 10 }}
                size={30}
                onPress={() => gobackRoute()}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.ADDRESS_ADD}
          component={AddressAddScreen}
          options={{
            presentation: 'modal',
            headerTitle: 'Địa chỉ nhận hàng',
            headerTitleStyle: { color: '#fff' },
            headerStyle: { backgroundColor: '#dc0000' },
            headerBackVisible: false,
            headerRight: props => (
              <Icon
                name={'arrow-back'}
                type="material"
                color="#fff"
                {...props}
                onPress={() => gobackRoute()}
                containerStyle={{ paddingRight: 10 }}
                size={30}
              />
            ),
          }}
        />
        <Stack.Screen
          name={ROUTES.PAYMENT_SUCCESS}
          component={PaymentSuccessScreen}
          options={{ headerShown: false }}
        />
      </Stack.Group>
    </Stack.Navigator>
  );
};
