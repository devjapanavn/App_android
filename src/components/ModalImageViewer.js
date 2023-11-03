import React, {} from 'react';
import Protypes from 'prop-types';
import _, {isEqual} from 'lodash';
import ImageView from 'react-native-image-viewing';
const ModalImageViewerComponent = ({images, index, visible, onClose}) => {
  return (
    <ImageView
      images={images}
      imageIndex={0}
      visible={visible}
      onRequestClose={onClose}
    />
  );
};

ModalImageViewerComponent.propTypes = {
  images: Protypes.array,
  index: Protypes.number,
  visible: Protypes.bool,
  onClose: Protypes.func,
};

function areEqual(prevProps, nextProps) {
  return (
    isEqual(prevProps.images, nextProps.images) &&
    isEqual(prevProps.index, nextProps.index) &&
    isEqual(prevProps.visible, nextProps.visible)
  );
}
export const ModalImageViewer = React.memo(ModalImageViewerComponent, areEqual);
