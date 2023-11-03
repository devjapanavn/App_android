import api from '@app/api';
import { colors, spacing } from '@app/assets';
import {
  BannerCarousel,
  ContentBlock,
  CountDownProducts,
  HeaderComponent,
  IconEvent,
  ImageBlock,
  ImageMap,
  ListNews,
  NewBannerCarousel,
  PopupNoti,
  ProductWithCategories,
} from '@app/components';
import { BLOCK_ENUM } from '@app/constants';
import { onHandleNotification, onPressLink } from '@app/utils';
import { useFocusEffect, useRoute } from '@react-navigation/native';
import _ from 'lodash';
import React from 'react';
import { useEffect, useState } from 'react';
import {
  StatusBar,
  StyleSheet,
  View,
  InteractionManager,
  BackHandler,
  RefreshControl,
} from 'react-native';
import Animated, {
  useAnimatedScrollHandler,
  useSharedValue,
} from 'react-native-reanimated';
import { SafeAreaView } from 'react-native-safe-area-context';
import Spinner from 'react-native-spinkit';
import { useQuery } from 'react-query';
import { HeaderSubMenu } from './component';
import messaging from '@react-native-firebase/messaging';
const fetch = async () => {
  return await api.getPageBlock();
};
const Screen = props => {
  const route = useRoute();
  const contentOffset = useSharedValue(0);
  const [onReady, setOnReady] = useState(false);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() => {
      setOnReady(true);
      initFirebaseNoti();
    });
    return () => {
      interactionPromise.cancel();
      setOnReady(false);
    };
  }, []);

  useFocusEffect(
    React.useCallback(() => {
      const onBackPress = () => {
        return true;
      };
      BackHandler.addEventListener('hardwareBackPress', onBackPress);

      return () =>
        BackHandler.removeEventListener('hardwareBackPress', onBackPress);
    }, []),
  );

  const { status, data, error, refetch, isLoading, isRefetching } = useQuery(
    ['getPageHomeBlock'],
    () => fetch(),
  );
  const handleScroll = useAnimatedScrollHandler(event => {
    contentOffset.value = event.contentOffset.y;
  });

  async function initFirebaseNoti() {

    messaging().onNotificationOpenedApp(remoteMessage => {
      console.log(
        'Notification caused app to open from background state:',
        remoteMessage.notification,
      );
      // navigateRoute(ROUTES.CHAT, null, null, true);
    });
    messaging()
      .getInitialNotification()
      .then(remoteMessage => {
        if (remoteMessage) {
          onHandleNotification(remoteMessage.data)
          // navigateRoute(ROUTES.CHAT, null, null, true);
        }
      });
  }

  const renderItem = ({ item, index }) => {
    switch (item.name_code) {
      case BLOCK_ENUM.BLOCK_HEADER: {
        if (index !== 0) return <HeaderComponent offsetY={contentOffset} />;
        return null;
      }
      case BLOCK_ENUM.BLOCK_MENU: {
        if (index !== 1)
          return (
            <HeaderSubMenu
              blockIndex={index}
              menu={item.data_block?.menu}
              showStype={item.data_block?.nang_cao?.mobile}
            />
          );
        return null;
      }
      case BLOCK_ENUM.BLOCK_CAROUSEL: {
        return (
          <BannerCarousel
            title={item.data_block ? item.data_block['tieu-de'] : null}
            type={item.data_block.nang_cao?.kieu_hien_thi}
            banners={item.data_block.banner}
            showDot={true}
            backgroundColor="#fff"
            showStype={item.data_block?.nang_cao?.mobile}
            onPress={onPressLink}
          />
        );
      }
      case BLOCK_ENUM.BLOCK_GALLERY: {
        return (
          <ImageBlock
            banners={item.data_block?.banner}
            showStype={item.data_block?.nang_cao?.mobile}
            onPressLink={onPressLink}
          />
        );
      }
      case BLOCK_ENUM.BLOCK_ICON: {
        return (
          <IconEvent
            icons={item.data_block.banner}
            onPress={val => onPressLink(val.link)}
            showStype={item.data_block?.nang_cao?.mobile}
          />
        );
      }
      case BLOCK_ENUM.BLOCK_COUNTDOW: {
        return (
          <>
            <CountDownProducts
              dataBlock={item.data_block}
              showStype={item.data_block?.nang_cao?.mobile}
            />
          </>
        );
      }
      case BLOCK_ENUM.BLOCK_PRODUCTS: {
        return (
          <View
            style={{
              backgroundColor:
                item.data_block?.nang_cao?.mobile?.color_background,
            }}>
            <ProductWithCategories
              header={() => (
                <ImageBlock
                  banners={item.data_block?.banner}
                  onPressLink={onPressLink}
                  inside={true}
                  showStype={{ element_padding: { left: 4, right: 4, top: 4, bottom: 4 } }}
                />
              )}
              indexBlock={item.id_block}
              menus={item.data_block?.blocks?.menu_tap}
              type={item.data_block?.nang_cao.kieu_hien_thi}
              showStype={item.data_block?.nang_cao?.mobile}
            />
          </View>
        );
      }
      case BLOCK_ENUM.BLOCK_NEWS: {
        return (
          <ListNews
            news={item.data_block?.news}
            title={item.data_block ? item.data_block['tieu-de']?.name : ''}
          />
        );
      }
      case BLOCK_ENUM.BLOCK_IMAGE_MAP: {
        return (
          <ImageMap
            idBlock={item.id}
            data={item.data_block}
            onPressLink={onPressLink}
            showStype={item.data_block?.nang_cao?.mobile}
          />
        );
      }
      case BLOCK_ENUM.BLOCK_CONTENT: {
        return (
          <ContentBlock
            idBlock={item.id}
            data={item.data_block}
            showStype={item.data_block?.nang_cao?.mobile}
          />
        );
      }
    }
  };

  const _renderHeader = () => {
    if (data && data.length > 0) {
      const headerIndex = _.findIndex(
        data,
        dt => dt.name_code === BLOCK_ENUM.BLOCK_HEADER,
      );
      const headerMenuIndex = _.findIndex(
        data,
        dt => dt.name_code === BLOCK_ENUM.BLOCK_MENU,
      );
      let jsx = [];
      if (headerIndex === 0) {
        jsx.push(
          <HeaderComponent key={'HeaderComponent'} offsetY={contentOffset} />,
        );
      }
      if (headerMenuIndex === 1) {
        jsx.push(
          <HeaderSubMenu
            key={'HeaderSubMenu'}
            blockIndex={1}
            menu={data[headerMenuIndex]?.data_block?.menu}
            showStype={data[headerMenuIndex]?.data_block?.nang_cao?.mobile}
          />,
        );
      }
      return <View>{jsx.map(item => item)}</View>;
    }
  };

  if (isLoading) {
    return (
      <SafeAreaView style={styles.container}>
        <HeaderComponent offsetY={contentOffset} />
        <View
          style={{
            justifyContent: 'center',
            alignItems: 'center',
            margin: spacing.large,
            flex: 1,
          }}>
          <Spinner type="Circle" color={colors.primary} size={40} />
        </View>
      </SafeAreaView>
    );
  }
  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#dc0000" />
      {_renderHeader()}
      {onReady ? (
        <>

          <Animated.FlatList
            refreshControl={
              <RefreshControl
                tintColor={colors.primary}
                refreshing={isRefetching}
                onRefresh={refetch}
              />
            }
            windowSize={11}
            onScroll={handleScroll}
            removeClippedSubviews={true}
            listKey={(item, index) => `Home_component__key_${item.id.toString()}`}
            key={'HOME_LIST_COMPONENT'}
            data={data || []}
            keyExtractor={item => 'Home_component_' + item.id}
            showsVerticalScrollIndicator={false}
            initialNumToRender={10}
            renderItem={renderItem}
          />
          <PopupNoti type={1} />
        </>
      ) : (
        <View style={styles.box} />
      )}
      {loading ? (
        <View
          style={{
            position: 'absolute',
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            backgroundColor: 'rgba(0,0,0,0.3)',
            zIndex: 11,
            justifyContent: 'center',
            alignItems: 'center',
          }}>
          <Spinner type="FadingCircleAlt" size={60} color="#fff" />
        </View>
      ) : null}
    </SafeAreaView>
  );
};

export const HomeScreen = Screen;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.white,
  },
  box: {
    flex: 1,
    backgroundColor: '#fff',
  },
});
