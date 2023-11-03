import React, {useEffect, useState} from 'react';
import {Image, ImagePropTypes, ViewPropTypes} from 'react-native';
import FastImage from 'react-native-fast-image';
import PropTypes from 'prop-types';
import _ from 'lodash';
const ImageReponsiveComponent = ({source, containerStyle}) => {
  const [ratio, setRatio] = useState(null);
  useEffect(() => {
    if (source && source.uri) {
      Image.getSize(source.uri, (width, height) => {
        const rt = width / height;
        setRatio(rt);
      });
    } else {
      const {width, height} = Image.resolveAssetSource(source);
      const rt = width / height;
      setRatio(rt);
    }
  }, []);
  if (source && source.uri) {
    return (
      <FastImage
        source={{uri: source.uri, priority: 'normal'}}
        style={[{aspectRatio: ratio}, containerStyle]}
        resizeMode="contain"
      />
    );
  } else {
    return (
      <Image
        source={source}
        resizeMethod="resize"
        resizeMode="contain"
        style={[containerStyle, {aspectRatio: ratio || 0, height: 'auto'}]}
      />
    );
  }
};
ImageReponsiveComponent.propTypes = {
  source: PropTypes.any,
  containerStyle: Image.propTypes.style,
};

function areEqual(prevProps, nextProps) {
  return (
    _.isEqual(prevProps.source, nextProps.source) &&
    _.isEqual(prevProps.containerStyle, nextProps.containerStyle)
  );
}

export const ImageReponsive = React.memo(ImageReponsiveComponent, areEqual);
// const styles = StyleSheet.create({
//   container: {},
//   reponsive: {
//     width: appDimensions.width,
//     height: null,
//   },
// });
