import {ROUTES} from '@app/constants';
import {gobackRoute, navigateRoute} from '@app/route';
import {Controller, useForm} from 'react-hook-form';
import {useRoute} from '@react-navigation/native';
import _, {round} from 'lodash';
import {colors, globalStyles, images} from '@app/assets';
import React, {useEffect, useState} from 'react';
import {
  StyleSheet,
  View,
  TouchableOpacity,
  useWindowDimensions,
} from 'react-native';
import Carousel, { Pagination } from 'react-native-snap-carousel';
import {Text, Image} from 'react-native-elements';
import {BottomSheet, Button, Divider, ListItem} from 'react-native-elements';
import {SafeAreaView} from 'react-native-safe-area-context';
import Spinner from 'react-native-spinkit';
import {ImageReponsive} from '@app/components';
import Video from 'react-native-video';

const Screen = props => {
  const route = useRoute();
  const [currentIndex, setCurrentIndex] = useState(0);
  const [isLoadding, setIsLoadding] = useState(false);

  const [onReady, setOnReady] = useState(false);
  const {width} = useWindowDimensions();

  console.log('route.params');
  console.log(route.params);
  console.log(onReady);
  console.log(currentIndex);
  useEffect(() => {
    setOnReady(true);
    if(route.params?.imageIndex){
        setCurrentIndex(route.params?.imageIndex)
    }
  }, [route.params?.images,route.params?.imageIndex]);

  const renderItems = ({item}) => {
    let checkvideo = item.split('.')[item.split('.').length - 1];
    let objVideo;
    let checkVideo = false;
    if (checkvideo == 'mp4') {
      checkVideo = true;
      objVideo = (
        <Video
          source={{uri: item}} // Can be a URL or a local file.
          resizeMethod="resize"
          resizeMode="contain"
          onLoad={() => {
          setIsLoadding(true)
            console.log('loadded!');
          }}
          style={styles.backgroundVideo}
        />
      );
    } else {
      objVideo = (
        <ImageReponsive
          source={{
            uri: item,
          }}
          containerStyle={{
            width: width,
          }}
        />
      );
    }

    return (
      <TouchableOpacity style={styles.box} >
        {objVideo}
        {
            (!isLoadding && checkVideo) ? 
            <View
                style={{
                justifyContent: 'center',
                alignItems: 'center',
                flex: 1,
                backgroundColor: '#000',
                position: 'absolute',
                width: '100%',
                height: '100%'
                }}
            >
                <Spinner type="Circle" color={colors.primary} size={40} />
            </View>
            :null
        }
      </TouchableOpacity>
    );
  };
  return (
    <SafeAreaView style={styles.box}>
      <Carousel
        data={route.params?.images}
        renderItem={renderItems}
        sliderWidth={width}
        itemWidth={width}
        layout="default"
        autoplay={false}
        loop={false}
        inactiveSlideScale={1}
        firstItem={currentIndex}
        onSnapToItem={setCurrentIndex}
        shouldOptimizeUpdates={true}

      />
      <Pagination
        dotsLength={route.params?.images?.length}
        activeDotIndex={currentIndex}
        containerStyle={{ backgroundColor: 'rgba(0, 0, 0, 0.75)' }}
        dotStyle={{
            width: 10,
            height: 10,
            borderRadius: 5,
            backgroundColor: 'rgba(255, 255, 255, 0.92)'
        }}
        inactiveDotOpacity={0.4}
        inactiveDotScale={0.6}
      />
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  box: {
    flex: 1,
    backgroundColor: '#000',
    position: 'relative',
    paddingBottom: 60,
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
  },
  frame: {
    width: '100%',
    height: '100%',
    position: 'absolute',
    zIndex: 1,
    top: 0,
    left: 0,
  },
  backgroundVideo: {
    width: '100%',
    height: '100%',
  },
});

export const ImageListScreen = Screen;
