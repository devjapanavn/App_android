import { spacing } from '@app/assets';
import React, { useState } from 'react';
import { StyleSheet, View } from 'react-native';
import { Pagination } from 'react-native-snap-carousel';
import { Text } from 'react-native-elements';
import { BLOCK_BANNER_TYPE } from '@app/constants';
import BannerSliders from './BannerSliders';
import styles from './styles';
import { stringHelper } from '@app/utils';
const component = ({
  onPress,
  title,
  backgroundColor,
  type,
  showDot,
  banners,
  showStype,
}) => {
  const [activeSlide, setActiveSlide] = useState(0);
  const [dotInSlider, setDotInSlide] = useState(
    type === BLOCK_BANNER_TYPE.CUBE || type === BLOCK_BANNER_TYPE.SLICK,
  );

  const styleContainer = {
    backgroundColor: showStype?.color_background || undefined,
    marginTop: stringHelper.formatToNumber(showStype?.margin?.top) || undefined,
    marginLeft:
      stringHelper.formatToNumber(showStype?.margin?.left) || undefined,
    marginRight:
      stringHelper.formatToNumber(showStype?.margin?.right) || undefined,
    marginBottom:
      stringHelper.formatToNumber(showStype?.margin?.bottom) || undefined,

    paddingLeft:
      stringHelper.formatToNumber(showStype?.padding?.left) || undefined,
    paddingRight:
      stringHelper.formatToNumber(showStype?.padding?.right) || undefined,
    paddingBottom:
      stringHelper.formatToNumber(showStype?.padding?.bottom) || undefined,
    paddingTop:
      stringHelper.formatToNumber(showStype?.padding?.top) || undefined,
  };

  const _renderPadding = () => {
    if (banners && banners.length > 0)
      return (
        <Pagination
          dotsLength={banners.length || 0}
          activeDotIndex={activeSlide}
          containerStyle={
            dotInSlider ? styles.pagingContainerInside : styles.pagingContainer
          }
          dotStyle={
            dotInSlider ? styles.pagingDotInsideStyle : styles.pagingDotStyle
          }
          inactiveDotStyle={styles.pagingDotInactiveStyle}
          inactiveDotOpacity={0.4}
          inactiveDotScale={1}
        />
      );
    return <View />;
  };
  //type, banners, onChange
  return (
    <View style={[styles.container, styleContainer]}>
      {title ? (
        <Text
          style={[
            styles.txtTitle,
            {
              color: title['mau-sac'] || undefined,
              fontWeight: title['font-weight'] || undefined,
            },
          ]}>
          {title.name}
        </Text>
      ) : null}

      <BannerSliders
        type={type}
        onPress={onPress}
        banners={banners}
        onChangeIndex={setActiveSlide}
        showStype={showStype}
      />
      {showDot ? _renderPadding() : null}
    </View>
  );
};
export const BannerCarousel = React.memo(component, () => true);
