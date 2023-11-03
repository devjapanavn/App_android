import React from 'react';
import {View, Text, TouchableOpacity, StyleSheet} from 'react-native';
import {check, PERMISSIONS, RESULTS, request} from 'react-native-permissions';
import {launchCamera, launchImageLibrary} from 'react-native-image-picker';
import Modal from 'react-native-modal';
import PropTypes from 'prop-types';
import {toastAlert} from '@app/utils';
import {Alert} from 'react-native';
import isEqual from 'lodash.isequal';
import deviceInfoModule from 'react-native-device-info';

const ImagePickerComponent = props => {
  const optionImagePicker = {
    maxWidth: 1920,
    maxHeight: 1080,
    selectionLimit: props.limitImage,
    mediaType: props.mediaType,
    width: 1920,
    height: 1080,
    durationLimit: 60,
    saveToPhotos: false,
  };
  function openLibrary(callback) {
    launchImageLibrary(optionImagePicker, res => {
      if (!res.didCancel) {
        onClose();
        callback(res.assets);
      }
      if (res.errorMessage) {
        toastAlert('Lỗi: ', res.errorMessage);
      }
    });
  }

  async function openCamera(isRecord, callback) {
    let option = {...optionImagePicker, mediaType: 'photo'};
    if (isRecord) {
      option = {...optionImagePicker, mediaType: 'video'};
    }
    const res = await launchCamera(option);
	console.log(res)
    if (!res.didCancel) {
      onClose();
      callback(res.assets);
    }
    if (res.errorMessage) {
      toastAlert('Lỗi: ', res.errorMessage);
    }
  }

  function onClose() {
    if (props.onCancel) {
      props.onCancel();
    }
  }

  function explainCameraPermission(isRecord) {
    check(PERMISSIONS.ANDROID.CAMERA).then(cameraANDROIDStatus => {
      console.log('cameraANDROIDStatus', cameraANDROIDStatus);
      switch (cameraANDROIDStatus) {
        case RESULTS.UNAVAILABLE:
          toastAlert('Thiếp bị này không hỗ trợ camera');
          break;
        case RESULTS.GRANTED:
          openCamera(isRecord, props.onPressCamera);
          break;
        case RESULTS.DENIED:
          Alert.alert(
            'Cung cấp quyền sử dụng',
            'Để sử dụng chức năng này, bạn cần cung cấp quyền sử dụng Camera.',
            [
              {
                text: 'Cung cấp ngay',
                onPress: () => {
                  request(PERMISSIONS.ANDROID.CAMERA).then(res => {
                    console.log('res', res);
                    if (res === 'granted') {
                      openCamera(isRecord, props.onPressCamera);
                    }
                  });
                },
              },
              {text: 'Huỷ'},
            ],
          );
          break;
      }
    });
  }
  async function explainReadAndWriteStoragePermission() {
    const androidVersion = await deviceInfoModule.getApiLevel();
    let permission = PERMISSIONS.ANDROID.ACCESS_MEDIA_LOCATION;
    if (androidVersion <= 28) {
      permission = PERMISSIONS.ANDROID.READ_EXTERNAL_STORAGE;
    }
    console.log('permission', permission);
    check(permission).then(res => {
      switch (res) {
        case RESULTS.UNAVAILABLE:
          toastAlert('Thiếp bị này không hỗ trợ ');
          break;
        case RESULTS.GRANTED:
          openLibrary(props.onPressLibrary);
          break;
        case RESULTS.DENIED:
          Alert.alert(
            'Cung cấp quyền sử dụng',
            'Để sử dụng chức năng này, bạn cần cung cấp quyền đọc hình ảnh.',
            [
              {
                text: 'Cung cấp ngay',
                onPress: () => {
                  request(permission).then(res => {
                    if (res === 'granted') {
                      openLibrary(props.onPressLibrary);
                    }
                  });
                },
              },
              {text: 'Huỷ'},
            ],
          );
          break;
      }
    });
  }

  return (
    <Modal
      isVisible={props.visible}
      onBackButtonPress={onClose}
      onBackdropPress={onClose}>
      <View
        style={{
          backgroundColor: '#FFF',
          marginHorizontal: 10,
          marginVertical: 50,
          borderRadius: 10,
        }}>
        <View
          style={{padding: 10, borderBottomWidth: 1, borderColor: 'lightgray'}}>
          <Text style={{fontSize: 18, textAlign: 'center', color: 'black'}}>
            Chọn hình{' '}
          </Text>
        </View>
        <View
          style={{
            paddingTop: 10,
            paddingHorizontal: 20,
          }}>
          <Text style={{padding: 5}}>Bạn muốn lấy hình từ đâu? </Text>
        </View>
        <View
          style={{
            justifyContent: 'center',
            padding: 10,
            paddingHorizontal: 20,
          }}>
          {props.enableVideo ? (
            <TouchableOpacity
              onPress={() => explainCameraPermission(true)}
              style={[styles.button, {backgroundColor: '#1976D2'}]}>
              <Text style={[styles.textButton, {fontWeight: 'normal'}]}>
                {' '}
                Quay phim{' '}
              </Text>
            </TouchableOpacity>
          ) : null}
          <TouchableOpacity
            onPress={() => explainCameraPermission(false)}
            style={[styles.button, {backgroundColor: '#1976D2'}]}>
            <Text style={[styles.textButton, {fontWeight: 'normal'}]}>
              {' '}
              Chụp hình{' '}
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            onPress={() => explainReadAndWriteStoragePermission()}
            style={[styles.button, {backgroundColor: '#1976D2'}]}>
            <Text style={[styles.textButton, {fontWeight: 'normal'}]}>
              {' '}
              Thư viện hình{' '}
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            onPress={onClose}
            style={[styles.button, {backgroundColor: 'red'}]}>
            <Text style={[styles.textButton, {fontWeight: 'normal'}]}>
              {' '}
              Đóng{' '}
            </Text>
          </TouchableOpacity>
        </View>
      </View>
    </Modal>
  );
};

ImagePickerComponent.propTypes = {
  visible: PropTypes.bool,
  enableVideo: PropTypes.bool,
  mediaType: PropTypes.string,
  limitImage: PropTypes.number,
  onCancel: PropTypes.func,
  onPressLibrary: PropTypes.func,
  onPressCamera: PropTypes.func,
};
ImagePickerComponent.defaultProps = {
  visible: false,
  limitImage: 1,
  mediaType: 'photo',
  enableVideo: false,
};

function areEqual(prevProps, nextProps) {
  return isEqual(prevProps.visible, nextProps.visible);
}

export const ImagePicker = React.memo(ImagePickerComponent, areEqual);

const styles = StyleSheet.create({
  button: {
    marginVertical: 5,
    padding: 10,
    borderRadius: 30,
  },
  textButton: {
    fontSize: 16,
    color: '#FFF',
    textAlign: 'center',
  },
});
