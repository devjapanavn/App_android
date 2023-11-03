import { appDimensions, colors, globalStyles, spacing } from '@app/assets';
import { stringHelper } from '@app/utils';
import React, { useEffect, useState } from 'react';
import {
  FlatList,
  ImageBackground,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
  Image,
  useWindowDimensions,
} from 'react-native';
import FastImage from 'react-native-fast-image';

const component = ({ onPressLink, showStype, data, idBlock }) => {
  const [ratio, setRatio] = useState(null);
  const { width } = useWindowDimensions();
  const styleContainer = {
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
  console.log('bottom', showStype?.color_background)

  if (data && data.blocks && data.blocks.images_mobile) {
    useEffect(() => {
      Image.getSize(data.blocks.images_mobile, (width, height) => {
        const rt = width / height;
        setRatio(rt);
      });
    }, []);
    const widthBackground =
      width -
      stringHelper.formatToNumber(showStype?.margin?.left) -
      stringHelper.formatToNumber(showStype?.margin?.right) -
      stringHelper.formatToNumber(showStype?.padding?.left) -
      stringHelper.formatToNumber(showStype?.padding?.right);
    return (
      <View style={{ backgroundColor: showStype?.color_background || undefined }}>
        <View style={styleContainer}>
          <ImageBackground
            source={{ uri: data.blocks.images_mobile }}
            style={{
              width: widthBackground,
              aspectRatio: ratio,
              position: 'relative',
            }}
            resizeMode="contain">
            {data?.mobile &&
              data?.mobile.rect_top_left &&
              data?.mobile.rect_top_left.map((item, index) => {
                return (
                  <TouchableOpacity
                    key={`${idBlock}_image_map_${index}`}
                    onPress={() =>
                      onPressLink(
                        data.mobile.link && data.mobile.link[index]
                          ? data.mobile.link[index]
                          : null,
                      )
                    }
                    style={{
                      position: 'absolute',
                      width: `${item.width}%`,
                      height: `${item.height}%`,
                      left: `${item.left}%`,
                      top: `${item.top}%`,
                    }}
                  />
                );
              })}
          </ImageBackground>
        </View>

      </View>
    );
  }
  return <View />;
};
export const ImageMap = React.memo(component, () => true);

const styles = StyleSheet.create({});
