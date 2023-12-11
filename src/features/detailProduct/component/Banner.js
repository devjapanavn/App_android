import {ImageReponsive} from '@app/components';
import _ from 'lodash';
import React, {useEffect, useState} from 'react';
import {
  StyleSheet,
  TouchableOpacity,
  useWindowDimensions,
  View,
} from 'react-native';
import Video from 'react-native-video';
import {Image, Text} from 'react-native-elements';
import ImageView from 'react-native-image-viewing';
import Carousel from 'react-native-snap-carousel';
import { navigateRoute } from '@app/route';
import { ROUTES } from '@app/constants';
import Spinner from 'react-native-spinkit';
import {colors} from '@app/assets';

const Component = ({banners, frame, icons}) => {
  const [currentIndex, setCurrentIndex] = useState(0);
  const [images, setImages] = useState([]);
  const [isLoadding, setIsLoadding] = useState(false);
  const {width} = useWindowDimensions();
  const iconPostion = () => {
    let icons2h,
      iconQuatang,
      iconGiamgia = null;
    if (icons.position_2h_icon_promotion) {
      icons2h = iconsView(
        icons.position_2h_icon_promotion,
        icons.icon_promotion.icon_giaonhanh2h_json,
      );
    }
    if (icons.position_giamgia_icon_promotion) {
      iconGiamgia = iconsView(
        icons.position_giamgia_icon_promotion,
        icons.icon_promotion.icon_giamgia_json,
        'giamgia',
      );
    }
    if (icons.position_gift_icon_promotion) {
      iconQuatang = iconsView(
        icons.position_gift_icon_promotion,
        icons.icon_promotion.icon_quatang_json,
      );
    }

    return (
      <>
        {icons2h}
        {iconQuatang}
        {iconGiamgia}
      </>
    );
  };
  const iconsView = (styleicon, objIcon, checkPercent = '') => {
    // let styleIconOb = {};
    // if(styleicon.top){
    //   styleIconOb.top = styleicon.top;
    // }
    // if(styleicon.right){
    //   styleIconOb.right = styleicon.right;
    // }
    // if(styleicon.left){
    //   styleIconOb.left = styleicon.left;
    // }
    // if(styleicon.bottom){
    //   styleIconOb.bottom = styleicon.bottom;
    // }
    // if(styleicon.width){
    //   styleIconOb.width = styleicon.width;
    // }
    // if(styleicon.height){
    //   styleIconOb.height = styleicon.height;
    // }

    return (
      <View style={[styles.flexCenterIcons, styleicon]}>
        {objIcon.icon_image_url ? (
          <Image
            resizeMethod="resize"
            resizeMode="contain"
            source={{
              uri: objIcon.icon_image_url,
            }}
            style={{width: '100%', height: '100%'}}
          />
        ) : null}
        {checkPercent ? (
          <Text
            style={{
              position: 'absolute',
              fontSize: 11,
              color: '#fff',
              width: '100%',
              textAlign: 'center'
            }}>
            {icons.percent}
          </Text>
        ) : null}
      </View>
    );
  };
  const showGaleryDialog = () =>{
    navigateRoute(ROUTES.IMAGES_LIST, { images,imageIndex:currentIndex });
  }
  const renderItems = ({item, index}) => {
    let checkvideo = item.split('.')[item.split('.').length - 1];
    let objVideo;
    let checkVideo = false;
    if(checkvideo == 'mp4'){
      checkVideo = true;
      objVideo = <Video 
      source={{uri: item}}   // Can be a URL or a local file.
      resizeMethod="resize"
      resizeMode="contain"
      onLoad={()=>{
        setIsLoadding(true)
        console.log('loadded!')
      }}
      style={styles.backgroundVideo} 
      />
    }else{
      objVideo = <ImageReponsive
      source={{
        uri: item,
      }}
      containerStyle={{
        width: width,
        height: width,
      }}
    />
    }
    return (
      <TouchableOpacity
      style={{display: 'flex', justifyContent: 'center', alignItems: 'center'}}
        onPress={() => {
          showGaleryDialog()
        }}>
        {objVideo}
        {iconPostion()}
        {(!isLoadding && checkVideo) ? (
          <View style={{position: 'absolute'}}>
            <Spinner type="Circle" color={colors.primary} size={40} />
          </View>
        ): null}
        
        <View style={styles.frame}>
          <Image
            resizeMethod="resize"
            resizeMode="contain"
            source={{uri: frame}}
            style={{width: '100%', height: '100%'}}
          />
        </View>
      </TouchableOpacity>
    );
  };
  useEffect(() => {
    const temp = _.map(banners, dt => dt.images);
    setImages(temp);
  }, [banners]);
  // console.log('icons');
  // console.log(icons);

  return (
    <View style={{backgroundColor: '#fff'}}>
      <View style={{height: width, position: 'relative', marginTop: 60}}>
        {images && images.length > 0 ? (
          <>
            <Carousel
              data={images}
              renderItem={renderItems}
              sliderWidth={width}
              itemWidth={width}
              itemHeight={width}
              sliderHeight={width}
              layout="default"
              autoplay={false}
              loop={false}
              inactiveSlideScale={1}
              onSnapToItem={setCurrentIndex}
              shouldOptimizeUpdates={true}
            />
            <View style={styles.paggingContainer}>
              <Text style={styles.pageText}>
                {currentIndex + 1}/{images.length}
              </Text>
            </View>
            <View></View>
            {/* <ImageView
              presentationStyle="overFullScreen"
              images={images.map(img => ({uri: img}))}
              imageIndex={imageViewer.index}
              swipeToCloseEnabled={true}
              visible={imageViewer.visible}
              onRequestClose={() => setImageViewer({visible: false, index: 0})}
            /> */}
          </>
        ) : null}
      </View>
    </View>
  );
};
function areEqual(prev, next) {
  return _.isEqual(prev.banners, next.banners);
}
export const BannerSlider = React.memo(Component, areEqual);
const styles = StyleSheet.create({
  backgroundVideo: {
    width: '100%',
    height: '100%',
  },
  paggingContainer: {
    width: 45,
    height: 22,
    backgroundColor: 'rgba(255, 255, 255, 0.75)',
    borderRadius: 11,
    position: 'absolute',
    bottom: 10,
    right: 10,
    justifyContent: 'center',
    alignItems: 'center',
  },
  position_absolute: {
    position: 'absolute',
  },
  fullWidth: {
    width: '100%',
    height: '100%',
  },
  flexCenterIcons: {
    position: 'absolute',
    display: 'flex',
    justifyContent: 'center',
    zIndex: 2,
  },
  pageText: {
    color: '#555',
    fontSize: 12,
    textAlign: 'center',
  },
  frame: {
    width: '100%',
    height: '100%',
    position: 'absolute',
    zIndex: 1,
    top: 0,
    left: 0,
  },
  iconsGift: {
    width: 70,
    height: 70,
    position: 'absolute',
    zIndex: 2,
    bottom: 0,
    left: 0,
  },
});
