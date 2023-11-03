import Animated, {
  Easing,
  Extrapolate,
  interpolate,
  interpolateColor,
  runOnJS,
  useAnimatedScrollHandler,
  useAnimatedStyle,
  useSharedValue,
  withDecay,
  withSpring,
  withTiming,
} from 'react-native-reanimated';
import {
  Benefit,
  CommentFooter,
  CommentHeader,
  CommentItem,
  Footer,
  Header,
  ModalProductBuyTogether,
  ProductDesciption,
  ProductDiscountCode,
  ProductGift,
  ProductHeader,
  ProductInfo,
  ProductSuggest,
  ProductVariants,
  ProductViewed,
} from './component';
import {ImageReponsive, PopupNoti} from '@app/components';
import {
  FlatList,
  InteractionManager,
  SectionList,
  StatusBar,
  StyleSheet,
  View,
  useWindowDimensions,
} from 'react-native';
import React, {useCallback, useEffect, useState} from 'react';
import {appDimensions, colors} from '@app/assets';
import {useDispatch, useSelector} from 'react-redux';
import {useIsFocused, useRoute} from '@react-navigation/native';

import {BannerSlider} from './component/Banner';
import FastImage from 'react-native-fast-image';
import {GLOBAL_FUNC, ROUTES} from '@app/constants';
import {SafeAreaView} from 'react-native-safe-area-context';
import _ from 'lodash';
import api from '@app/api';
import {getTotalCart} from '@app/store/auth/services';
import {navigateRoute} from '@app/route';
import {toastAlert} from '@app/utils';
import {useQuery} from 'react-query';

const AnimateSectionList = Animated.createAnimatedComponent(SectionList);
const fetch = async (id, userId, direction, keyword) => {
  return await api.getDetailProduct(id, userId, direction, keyword);
};

const typeSection = {
  BANNER_PRODUCT: 'BANNER_PRODUCT',
  INFOMATION: 'INFOMATION',
  COUNTDOWN: 'COUNTDOWN',
  PRODUCT_BUY_TOGETHER: 'PRODUCT_BUY_TOGETHER',
  PRODUCT_COUPON: 'PRODUCT_COUPON',
  BANNER_IMAGE: 'BANNER_IMAGE',
  BENEFIT: 'BENEFIT',
  COMMENTS: 'COMMENTS',
  INFO_SPEC: 'INFO_SPEC',
  DESCRIPTION: 'DESCRIPTION',
  PRODUCTS_VIEWED: 'PRODUCTS_VIEWED',
  PRODUCT_VARIANT: 'PRODUCT_VARIANT',
  PRODUCT_GIFT: 'PRODUCT_GIFT',
};

const tempSection = [
  {type: typeSection.BANNER_PRODUCT, data: []},
  {type: typeSection.INFOMATION, data: []},
  {type: typeSection.PRODUCT_GIFT, data: []},
  {type: typeSection.PRODUCT_VARIANT, data: []},
  {type: typeSection.PRODUCT_BUY_TOGETHER, data: []},
  {type: typeSection.PRODUCT_COUPON, data: []},
  {type: typeSection.BANNER_IMAGE, data: []},
  {type: typeSection.BENEFIT, data: []},
  {type: typeSection.COMMENTS, data: []},
  {type: typeSection.INFO_SPEC, data: []},
  {type: typeSection.DESCRIPTION, data: []},
  {type: typeSection.PRODUCTS_VIEWED, data: []},
];

const BUY_TYPE = {
  ADD_TO_CART: 'ADD_TO_CART',
  BUY_TOGETHER: 'BUY_TOGETHER',
  BUY_NOW: 'BUY_NOW',
};

const Screen = props => {
  const isFocus = useIsFocused();
  const route = useRoute();
  const dispatch = useDispatch();
  const {width, height} = useWindowDimensions();
  const contentOffset = useSharedValue(0);
  const cartFly = useSharedValue(0);
  const [onReady, setOnReady] = useState(false);
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [detail, setDetail] = useState(null);
  const [modalVisible, setModalVisible] = useState(false);
  const [sectionList, setSectionList] = useState(tempSection);
  const [triggerCart, setTriggerCart] = useState(0);
  const {isLogin, user} = useSelector(state => ({
    isLogin: state.auth.isLogin,
    user: state.auth.user,
  }));

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
      setOnReady(false);
    };
  }, []);

  const handleScroll = useAnimatedScrollHandler(event => {
    contentOffset.value = event.contentOffset.y;
  });
  // console.log('route.params', route.params);
  const {status, data, error, refetch} = useQuery(
    ['getDetailProduct', {id: route.params?.id || 0}],
    () =>
      fetch(
        route.params?.id,
        user?.id || 0,
        route.params?.direction || '',
        route.params?.keyword || '',
      ),
    {
      enabled: isFocus && onReady,
      refetchOnWindowFocus: true,
    },
  );

  // const {status, data, error, refetch} = useQuery(
  //   ['getDetailProduct', {id: 16444 || 0}],
  //   () => fetch(16444),
  //   {
  //     enabled: isFocus && onReady,
  //   },
  // );

  const toggleModal = useCallback(() => {
    setModalVisible(prev => !prev);
  }, [modalVisible]);
console.log(data)
  useEffect(() => {
    if (data && data.detail) {
      // console.log('deltail: ')
      // console.log(data.detail.gift)
      setDetail(GLOBAL_FUNC.filterPrice(data.detail));
      setSelectedProduct({
        id: data.detail.id,
        images: data.detail.images,
        name: data.detail.name_vi,
        product_gift: data.detail.gift || null,
        percent: data.detail.percent,
        price_kythuat: data.detail.price_kythuat,
        is_check_kythuat: data.detail.is_check_kythuat,
        price_promotion: data.detail.price_promotion,
        price_goc: data.detail.price_goc,
        price: data.detail.price,
        status_num: data.detail.status_num,
        date_start: data.detail.date_start,
        date_end: data.detail.date_end,
        mota: data.detail.mota,
      });
      let newSectionList = _.map(tempSection, section => {
        let newData = section;
        switch (newData.type) {
          case typeSection.BANNER_IMAGE:
            newData.data = data.blockpage?.app?.multi_images;
            break;
          case typeSection.COMMENTS:
            newData.data = data.comments ? _.take(data.comments, 2) : [];
            break;
          default:
            break;
        }
        return newData;
      });
      setSectionList(newSectionList);
      api.updateViewed({id: route.params?.id, member_id: user?.id || 0});
    }
  }, [data]);

  function onTriggerCart() {
    setTriggerCart(prev => prev + 1);
  }
  async function addToCart(type) {
    if (!isLogin) {
      navigateRoute(ROUTES.LOGIN);
      return;
    }
    try {
      switch (type) {
        case BUY_TYPE.ADD_TO_CART:
          {
            cartFly.value = withTiming(
              1,
              {
                duration: 1000,
                easing: Easing.ease,
              },
              isFinished => {
                if (isFinished) {
                  runOnJS(onTriggerCart)();
                }
              },
            );
            setTimeout(async () => {
              const res = await api.addToCart(selectedProduct?.id, 1, user?.id);
              cartFly.value = 0;
              toastAlert('Đã thêm vào giỏ hàng!');
              dispatch(getTotalCart(user?.id));
            }, 1000);
          }
          break;
        default:
          const res = await api.addToCart(selectedProduct?.id, 1, user?.id);
          dispatch(getTotalCart(user?.id));
          if (type === BUY_TYPE.BUY_NOW) {
            setTimeout(() => {
              navigateRoute(ROUTES.CART_LIST, null, null, true);
            }, 500);
          }

          break;
      }
    } catch (error) {}
  }

  const searchBarStyle = useAnimatedStyle(() => {
    const searchBarBackgroundColor = interpolateColor(
      contentOffset.value,
      [0, appDimensions.height / 2.75],
      ['rgba(0,0,0,0)', colors.white],
    );
    const elevationNo = interpolate(
      contentOffset.value,
      [0, appDimensions.height / 2.75],
      [0, 2],
    );

    return {
      position: 'absolute',
      top: 0,
      width: '100%',
      backgroundColor: searchBarBackgroundColor,
      zIndex: 2,
      elevation: elevationNo,
    };
  });

  const cardFlyStyle = useAnimatedStyle(() => {
    const scale = interpolate(cartFly.value, [0, 0.2, 0.8, 1], [0, 1, 1, 0]);

    const translateX = interpolate(
      cartFly.value,
      [0, 0.3, 0.8, 1],
      [0, 30, width / 3, width - (width * 1) / 4 - 50],
    );
    const translateY = interpolate(
      cartFly.value,
      [0, 0.3, 0.5, 1],
      [0, -(height / 3), -(height / 2), -(height - 10 - 120)],
    );
    return {
      transform: [
        {translateX: translateX},
        {translateY: translateY},
        {scale: scale},
      ],
      position: 'absolute',
      bottom: 10,
      left: (width * 1) / 4,
    };
  });

  const renderSectionHeader = ({section}) => {

    switch (section.type) {
      case typeSection.BANNER_PRODUCT:
        return <BannerSlider banners={detail.multi_images} frame={detail.promotion_info?.image_frame} icons={detail}/>;
      case typeSection.INFOMATION:
        return (
          <ProductHeader detail={detail} selectedProduct={selectedProduct} />
        );

      case typeSection.PRODUCT_GIFT:
        if (selectedProduct?.product_gift) {
          return <FlatList style={styles.section}
          data={selectedProduct?.product_gift}
          renderItem={({item}) => <ProductGift gift={item} />}
          keyExtractor={item => item.product_name+item.id_product}/>
        }
        return null;
      case typeSection.PRODUCT_VARIANT:
        if (data?.variant) {
          return (
            <ProductVariants
              variant={data?.variant}
              onSelectVariant={variant => {
                setSelectedProduct({
                  id: variant.id_product_variation,
                  name_vi: variant.name_vi,
                  images: variant.images,
                  product_gift: variant.product_gift,
                  percent: variant.percent,
                  price_kythuat: variant.price_kythuat,
                  is_check_kythuat: variant.is_check_kythuat,
                  price_promotion: variant.price_promotion,
                  price_goc: variant.price_goc,
                  price: variant.price,
                  status_num: variant.status_num,
                  date_start: variant.date_start,
                  date_end: variant.date_end,
                  mota: variant.mota,
                });
              }}
            />
          );
        }
        return null;
      case typeSection.PRODUCT_BUY_TOGETHER:
        if (data?.bought_together && data?.bought_together?.list?.length > 0)
          return (
            <ProductSuggest
              productPrice={detail.price}
              productPricePromotion={detail.price_goc}
              productImage={detail.images}
              suggestionData={data?.bought_together || null}
              addToCart={() => addToCart(BUY_TYPE.BUY_TOGETHER)}
              onPressViewMore={toggleModal}
              mainProductId={route.params?.id}
            />
          );
        return null;
      case typeSection.PRODUCT_COUPON:
        if (data?.voucher_list && !_.isEmpty(data?.voucher_list))
          return <FlatList 
          data={data?.voucher_list}
          renderItem={({item}) => <ProductDiscountCode coupon={item} />}
          keyExtractor={item => item.code}/>;
        return null;

      case typeSection.BENEFIT:
        return <Benefit />;
      case typeSection.COMMENTS:
        return (
          <CommentHeader
            dataRating={data?.data_rating}
            mainProduct={{
              id: route.params?.id,
              image: data?.productImagePopup || data?.detail?.images || null,
              name: data?.detail?.name_vi,
              price: data?.detail?.price,
              price_promotion: data?.detail?.price_promotion,
              sku: data?.detail?.sku,
              dataRate: data?.data_rating,
            }}
          />
        );
      case typeSection.INFO_SPEC:
        return <ProductInfo detail={detail} />;
      case typeSection.DESCRIPTION:
        return <ProductDesciption description={detail?.desc_vi} />;
      case typeSection.PRODUCTS_VIEWED:
        return <ProductViewed id={route.params?.id} />;
      default:
        return <View />;
    }
  };

  const renderSectionItem = ({section, item}) => {
    switch (section.type) {
      case typeSection.BANNER_IMAGE:
        if (item && !_.isEmpty(item)) {
          return (
            <View style={[styles.section, {padding: 0}]}>
              <ImageReponsive
                source={{uri: item}}
                containerStyle={{width: appDimensions.width}}
              />
            </View>
          );
        }
        return <View />;
      case typeSection.COMMENTS:
        return (
          <CommentItem
            comment={item}
            mainProduct={{
              id: route.params?.id,
              image: data?.productImagePopup || data?.detail?.images || null,
              name: data?.detail?.name_vi,
              price: data?.detail?.price,
              price_promotion: data?.detail?.price_promotion,
              sku: data?.detail?.sku,
              dataRate: data?.data_rating,
            }}
          />
        );
      default:
        return <View />;
    }
  };

  const renderSectionFooter = ({section}) => {
    switch (section.type) {
      case typeSection.COMMENTS:
        return (
          <CommentFooter
            ratings={data?.data_rating}
            mainProduct={{
              id: route.params?.id,
              image: data?.productImagePopup || data?.detail?.images || null,
              name: data?.detail?.name_vi,
              price: data?.detail?.price,
              price_promotion: data?.detail?.price_promotion,
              sku: data?.detail?.sku,
              dataRate: data?.data_rating,
            }}
          />
        );
    }
  };
  return (
    <SafeAreaView style={styles.container}>
      <StatusBar translucent backgroundColor="transparent" />
      <Animated.View style={searchBarStyle}>
        <Header offsetY={contentOffset} triggerCart={triggerCart} />
      </Animated.View>
      {onReady && detail && isFocus ? (
        <>
          <AnimateSectionList
            contentContainerStyle={{
              backgroundColor: 'rgba(189, 189, 189,0.3)',
              flexGrow: 1,
            }}
            sections={tempSection}
            renderItem={renderSectionItem}
            renderSectionHeader={renderSectionHeader}
            onScroll={handleScroll}
            renderSectionFooter={renderSectionFooter}
          />

          <Footer
            addToCart={addToCart}
            statusNum={selectedProduct.status_num}
            thumbnailImage={selectedProduct.images}
          />
          {data?.bought_together ? (
            <ModalProductBuyTogether
              visible={modalVisible}
              boughtTogether={data?.bought_together}
              onClose={toggleModal}
              onPressBuy={() => {
                toggleModal();
                setTimeout(() => {
                  addToCart();
                }, 300);
              }}
              mainProduct={{
                id: route.params?.id,
                image: data?.productImagePopup || data?.detail?.images || null,
                name: data?.detail?.name_vi,
                price: data?.detail?.price,
                price_promotion: data?.detail?.price_promotion,
                sku: data?.detail?.sku,
              }}
            />
          ) : null}
        </>
      ) : null}
      <PopupNoti type={2} />
      {selectedProduct?.images ? (
        <Animated.View style={cardFlyStyle}>
          <FastImage
            source={{uri: selectedProduct.images}}
            style={{width: 30, height: 30}}
            resizeMode={'contain'}
          />
        </Animated.View>
      ) : null}
    </SafeAreaView>
  );
};

export const DetailProductScreen = Screen;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.white,
  },
  m_10: {
    padding: 10,
  },
  section: {
    padding: 10,
    backgroundColor: '#fff',
    marginBottom: 4,
    elevation: 1,
  },
  divide: {
    width: 1,
    height: 36,
    backgroundColor: '#fff',
  },
  imagePopup: {
    width: 25,
    height: 25,
    resizeMode: 'contain',
    position: 'absolute',
    right: appDimensions.width / 1.75,
  },
});
