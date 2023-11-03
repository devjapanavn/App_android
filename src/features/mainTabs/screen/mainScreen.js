import React, { useEffect } from 'react';
import _ from 'lodash';
import { useSelector } from 'react-redux';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { ROUTES } from '@app/constants';
import { images } from '@app/assets';
import { HomeScreen } from '@app/features/home';
import { Image } from 'react-native';
import { Icon } from 'react-native-elements';
import { AccountScreen } from '@app/features/account';
import { CustomeTabBar } from '../components';
import { EventScreen } from '@app/features/event';
import { useRoute } from '@react-navigation/native';
import { tabNavigate } from '@app/route';
import { NotificationScreen } from '@app/features/notification';
import Svg, { Path } from 'react-native-svg';
const Tab = createBottomTabNavigator();

const Screen = () => {
  const route = useRoute();
  const { id_page_home, id_page_promotion } = useSelector(state => ({
    id_page_home: state.root.id_page_home,
    id_page_promotion: state.root.id_page_promotion,
  }));

  useEffect(() => {
    if (route.params && route.params.jumbTab) {
      setTimeout(() => {
        tabNavigate(route.params.jumbTab);
      }, 300);
    }
  }, []);

  return (
    <Tab.Navigator
      screenOptions={{ headerShown: false }}
      initialRouteName={ROUTES.HOME_TAB}
      backBehavior="initialRoute"
      tabBar={props => <CustomeTabBar {...props} />}>
      <Tab.Screen
        name={ROUTES.ACCOUNT}
        component={AccountScreen}
        options={{
          title: 'Cá nhân',
          tabBarIcon: ({ color, size, focused }) => (
            <Icon
              name="md-person-sharp"
              type="ionicon"
              color={color}
              size={size}
            />
          ),
        }}
      />
      <Tab.Screen
        name={ROUTES.HOME_TAB}
        component={HomeScreen}
        options={{
          title: 'Trang chủ',
          tabBarIcon: ({ color, size, focused }) => (
            <Svg height={size} width={size} color={color}>
              <Path d="M 20.45 8.894 L 11.211 0.297 C 10.806 -0.08 10.194 -0.08 9.789 0.297 L 0.55 8.894 C 0.225 9.196 0.118 9.662 0.277 10.08 C 0.435 10.499 0.822 10.769 1.261 10.769 L 2.737 10.769 L 2.737 19.382 C 2.737 19.723 3.008 20 3.342 20 L 8.406 20 C 8.741 20 9.012 19.723 9.012 19.382 L 9.012 14.152 L 11.988 14.152 L 11.988 19.382 C 11.988 19.723 12.259 20 12.594 20 L 17.657 20 C 17.992 20 18.263 19.723 18.263 19.382 L 18.263 10.769 L 19.739 10.769 C 20.178 10.769 20.565 10.499 20.723 10.08 C 20.882 9.662 20.775 9.196 20.45 8.894" fill={color}></Path>
            </Svg>
          ),
        }}
        initialParams={{ id: id_page_home }}
      />
      <Tab.Screen
        name={ROUTES.NOTIFICATION}
        component={NotificationScreen}
        options={{
          headerShown: true,
          headerTitle: 'Thông báo',
          headerTitleAlign: 'center',
          title: 'Thông báo',
          headerBackgroundContainerStyle: { backgroundColor: '#fff' },
          tabBarIcon: ({ color, size, focused }) => (
            <Icon name="bell" type="feather" color={color} size={size} />
          ),
        }}
      />
      <Tab.Screen
        name={ROUTES.CONTACT}
        component={HomeScreen}
        options={{
          title: 'Liên hệ',
          tabBarIcon: ({ color, size, focused }) => (
            <Image source={images.chat_bubbles} style={{ width: size, height: size, tintColor: color }} resizeMode="contain" />
          ),
        }}
      />
    </Tab.Navigator>
  );
};

export const MainTabsScreen = React.memo(Screen);
