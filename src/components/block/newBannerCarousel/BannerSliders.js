import { appDimensions, spacing } from '@app/assets';
import React from 'react';
import { TouchableOpacity, useWindowDimensions, View } from 'react-native';
import Carousel, { getInputRangeFromIndexes } from 'react-native-reanimated-carousel';
import { BLOCK_BANNER_TYPE } from '@app/constants';
import { useIsFocused } from '@react-navigation/native';
import styles from './styles';
import { ImageReponsive } from 'src/components/imageReponsive';
import { stringHelper, useScreenDimensions } from '@app/utils';

const BannerItemCube = React.memo(
  ({ onPress, image }) => {
    return (
      <TouchableOpacity
        activeOpacity={0.9}
        onPress={onPress}
      >
        <ImageReponsive
          source={{ uri: image }}
          containerStyle={{ width: '100%' }}
          resizeMode="stretch"
        />
      </TouchableOpacity>
    );
  },
  (prev, next) => prev.onPress === next.onPress && prev.image === next.image,
);
const BannerItemCoverFlow = React.memo(
  ({ onPress, image }) => {
    return (
      <TouchableOpacity
        activeOpacity={0.9}
        onPress={onPress}
        style={{ overflow: 'hidden' }}>
        <ImageReponsive
          source={{ uri: image }}
          style={{ width: 270, height: 180 }}
          resizeMode="contain"
        />
      </TouchableOpacity>
    );
  },
  (prev, next) => prev.onPress === next.onPress && prev.image === next.image,
);
const BannerItemSlick = React.memo(
  ({ onPress, image }) => {
    return (
      <TouchableOpacity
        activeOpacity={0.9}
        onPress={onPress}
        style={{ overflow: 'hidden' }}>
        <ImageReponsive
          source={{ uri: image }}
          style={{ width: '100%' }}
          resizeMode="contain"
        />
      </TouchableOpacity>
    );
  },
  (prev, next) => prev.onPress === next.onPress && prev.image === next.image,
);
const BannerItemWheel = React.memo(
  ({ onPress, image }) => {
    const { width } = useWindowDimensions()
    return (
      <TouchableOpacity
        activeOpacity={0.9}
        onPress={onPress}
      >
        <ImageReponsive
          source={{ uri: image }}
          containerStyle={{ width: width / 3.3 }}
        />
      </TouchableOpacity>
    );
  },
  (prev, next) => prev.onPress === next.onPress && prev.image === next.image,
);

export default React.memo(
  ({ type, banners, onChangeIndex, onPress, showStype }) => {
    console.log('type', type)
    const left =
      stringHelper.formatToNumber(showStype?.padding?.left) + stringHelper.formatToNumber(showStype?.margin?.left);
    const right =
      stringHelper.formatToNumber(showStype?.padding?.right) + stringHelper.formatToNumber(showStype?.margin?.right);
    const leftElement =
      stringHelper.formatToNumber(showStype?.element_padding?.left);
    const rightElement =
      stringHelper.formatToNumber(showStype?.element_padding?.right);
    const screenDimention = useScreenDimensions();
    const isFocus = useIsFocused();

    const _whellScrollInterpolator = (index, carouselProps) => {
      const range = [3, 2, 1, 0, -1];
      const inputRange = getInputRangeFromIndexes(range, index, carouselProps);
      const outputRange = range;

      return { inputRange, outputRange };
    };

    const _wheelAnimatedStyles = (index, animatedValue, carouselProps) => {
      return {
        zIndex: carouselProps.data.length - index,
        opacity: animatedValue.interpolate({
          inputRange: [2, 3],
          outputRange: [1, 0],
        }),
        transform: [
          {
            translateX: animatedValue.interpolate({
              inputRange: [-1, 0, 1],
              outputRange: ['70%', '30%', '0%'],
              extrapolate: 'clamp',
            }),
            scale: animatedValue.interpolate({
              inputRange: [-1, 0, 1],
              outputRange: [0.9, 1, 0.9],
              extrapolate: 'clamp',
            }),
            rotateY: animatedValue.interpolate({
              inputRange: [-1, 0, 1],
              outputRange: ['45deg', '0deg', '-45deg'],
              extrapolate: 'clamp',
            }),
          },
        ],
      };
    };

    const _renderItem = ({ item, index }) => {
      switch (type) {
        case BLOCK_BANNER_TYPE.CUBE: {
          return (
            <BannerItemCube
              onPress={() => onPress(item.link)}
              image={item.images_mobile}
            />
          );
        }
        case BLOCK_BANNER_TYPE.COVER_FLOW:
          return (
            <BannerItemCoverFlow
              onPress={() => onPress(item.link)}
              image={item.images_mobile}
            />
          );

        case BLOCK_BANNER_TYPE.WHEEL:
          return (
            <BannerItemWheel
              onPress={() => onPress(item.link)}
              image={item.images_mobile}
            />
          );
        case BLOCK_BANNER_TYPE.SLICK:
          return (
            <BannerItemSlick
              onPress={() => onPress(item.link)}
              image={item.images_mobile}
            />
          );
        default:
          return (
            <BannerItemCube
              onPress={() => onPress(item.link)}
              image={item.images_mobile}
            />
          );
      }
    };
    if (!banners || banners.length === 0) {
      return <View />;
    }
    console.log('type', type)

    switch (type) {
      case BLOCK_BANNER_TYPE.CUBE: {
        return (
            <Carousel
              key={`banner_${type}`}
              autoPlayInterval={3000}
              loop
              autoPlay={isFocus}
              data={banners || []}
              renderItem={_renderItem}
              width={screenDimention.width - left - right}
              height={screenDimention.width * 0.6}
              pagingEnabled={true}
              onSnapToItem={onChangeIndex}
              mode="parallax"
              modeConfig={{
                parallaxScrollingScale: 0.9,
                parallaxScrollingOffset: 50,
              }}
            />
        );
      }
      // case BLOCK_BANNER_TYPE.COVER_FLOW:
      //   const widthBanner =
      //     screenDimention.width - left - right > 350 ? 270 : screenDimention.width - 100;
      //   return (
      //     <Carousel
      //       keyExtractor={(item, index) =>
      //         `banner_${index}_${type}_${item.images_mobile}`
      //       }
      //       data={banners || []}
      //       loop
      //       loopClonesPerSide={banners.length}
      //       renderItem={_renderItem}
      //       sliderWidth={screenDimention.width - left - right}
      //       itemWidth={widthBanner}
      //       pagingEnabled={true}
      //       activeSlideAlignment="start"
      //       onSnapToItem={onChangeIndex}
      //       scrollInterpolator={_whellScrollInterpolator}
      //       slideInterpolatedStyle={_wheelAnimatedStyles}
      //       useScrollView={true}
      //     />
      //   );
      case BLOCK_BANNER_TYPE.WHEEL:
        return (
            <Carousel
              key={`banner_${type}`}
              autoPlayInterval={5000}
              loop
              autoPlay={isFocus}
              data={banners || []}
              renderItem={_renderItem}
              width={screenDimention.width - left - right}
              height={screenDimention.width * 0.6}
              pagingEnabled={true}
              onSnapToItem={onChangeIndex}
              mode="parallax"
              modeConfig={{
                parallaxScrollingScale: 0.9,
                parallaxScrollingOffset: 50,
              }}
            />
        );
      // case BLOCK_BANNER_TYPE.SLICK:
      //   return (
      //     <Carousel
      //       keyExtractor={(item, index) =>
      //         `banner_${index}_${type}_${item.images_mobile}`
      //       }
      //       data={banners || []}
      //       loop
      //       loopClonesPerSide={banners.length}
      //       renderItem={_renderItem}
      //       sliderWidth={screenDimention.width - left - right}
      //       itemWidth={screenDimention.width}
      //       layout="default"
      //       activeSlideAlignment="start"
      //       inactiveSlideOpacity={1}
      //       shouldOptimizeUpdates
      //       removeClippedSubviews={true}
      //       onSnapToItem={onChangeIndex}
      //     />
      //   );

      default:
        return (
          <Carousel
            key={`banner_${type}`}
            autoPlayInterval={5000}
            loop
            autoPlay={isFocus}
            data={banners || []}
            renderItem={_renderItem}
            width={screenDimention.width - left - right}
            height={300}
            pagingEnabled={true}
            onSnapToItem={onChangeIndex}
          />
        );
    }
  },
  (prev, next) => prev.onPress === next.onPress,
);
