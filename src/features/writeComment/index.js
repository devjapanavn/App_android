import api from '@app/api';
import { appDimensions, images } from '@app/assets';
import { ImagePicker, ModalImageViewer } from '@app/components';
import { gobackRoute } from '@app/route';
import { useRoute } from '@react-navigation/native';
import _ from 'lodash';
import React, { useCallback, useEffect, useState } from 'react';
import {
  StyleSheet,
  View,
  InteractionManager,
  FlatList,
  ScrollView,
  Image,
} from 'react-native';
import { Button, Divider, Icon, Input, Text } from 'react-native-elements';
import FastImage from 'react-native-fast-image';
import Animated, {
  Easing,
  useAnimatedStyle,
  useSharedValue,
  withTiming,
} from 'react-native-reanimated';
import StarRating from 'react-native-star-rating';
import { iOSColors } from 'react-native-typography';
import { useSelector } from 'react-redux';
import DeviceInfo from 'react-native-device-info';
import { toastAlert } from '@app/utils';

const Screen = props => {
  const route = useRoute();
  const { user } = useSelector(state => ({
    user: state.auth.user,
  }));
  const [onReady, setOnReady] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [rating, setRating] = useState(5);
  const [note, setNote] = useState('');
  const [name, setName] = useState(user?.name);
  const [phone, setPhone] = useState(user?.mobile);
  const [modalImagePicker, setModalImagePicker] = useState(false);
  const [imageComments, setImageComments] = useState([]);
  const ty = useSharedValue(appDimensions.height);


  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  function checkModel() {
    if (_.isEmpty(note)) {
      toastAlert('Bạn chưa nhập đánh giá!');
      return false;
    }
    if (note.trim().length < 10) {
      toastAlert('Đánh giá ít nhất 10 kí tự');
      return false;
    }
    if (_.isEmpty(name)) {
      toastAlert('Bạn chưa nhập tên!');
      return false;
    }
    if (_.isEmpty(phone)) {
      toastAlert('Bạn chưa nhập số điện thoại!');
      return false;
    }
    return true;
  }
  const submit = async () => {
    const modalSubmit = {
      id_member: user?.id,
      device: DeviceInfo.getUniqueId(),
      id_product: route.params?.id,
      slug: route.params?.slug || '',
      phone: phone,
      fullname: name,
      rate: rating,
      comments: note,
    };
    if (imageComments && imageComments.length > 0) {
      modalSubmit['images[]'] = imageComments
    }
    console.log('modalSubmit', modalSubmit);
    if (checkModel()) {
      setIsLoading(true);
      try {
        const res = await api.addComment(modalSubmit);
        console.log(res)
        setIsLoading(false);
        toastAlert('Đã gửi đánh giá thành công!');
        ty.value = withTiming(0, {
          duration: 300,
          easing: Easing.sin,
        });
      } catch (error) {
        console.log(error)
        setIsLoading(false);
      }

    }
  };

  const reviewText = () => {
    switch (rating) {
      case 1:
        return 'Tệ';
      case 2:
        return 'Không tốt';
      case 3:
        return 'Bình thường';
      case 4:
        return 'Tốt';
      case 5:
        return 'Rất tốt';
      default:
        break;
    }
  };
  const containerAnimateStyle = useAnimatedStyle(() => {
    return {
      transform: [{ translateY: ty.value }],
    };
  });

  function onUpload(imgs) {
    if (imgs && imgs.length > 0) {
      const imageUploads = _.map(imgs, (img, index) => ({
        index: index,
        uri: img.uri,
        type: img.type,
        name: img.fileName,
      }));
      let imageTemp = [...imageComments, ...imageUploads];
      if (imageTemp.length > 4) {
        _.takeRight(imageTemp, 4);
      }
      setImageComments(imageTemp);
    }
  }

  const renderProductInfo = () => (
    <View style={styles.productContainer}>
      <FastImage
        source={{ uri: route.params?.image }}
        style={styles.imageProduct}
      />
      <Text style={styles.productName}>{route.params?.name}</Text>
    </View>
  );

  const renderFormRating = () => {
    return (
      <View style={{ alignItems: 'center' }}>
        <Text style={styles.ratingText}>{reviewText()}</Text>
        <StarRating
          starStyle={styles.rowRatingStar}
          starSize={40}
          maxStars={5}
          rating={rating}
          selectedStar={setRating}
          fullStarColor={iOSColors.yellow}
          fullStar={images.ic_star}
          emptyStar={images.ic_star_inactive}
        />
        <Input
          multiline={true}
          textAlignVertical="top"
          numberOfLines={3}
          placeholder="Đánh giá của bạn..."
          placeholderTextColor="#888"
          inputContainerStyle={{ borderBottomWidth: 0 }}
          onChangeText={setNote}
          autoCorrect={false}
          containerStyle={styles.noteInputContainer}
        />
        <View style={styles.note}>
          <Text style={styles.noteText}>
            {note.length} ký tự - Tối thiểu 10
          </Text>
          <Button
            onPress={() => setModalImagePicker(true)}
            titleStyle={{ fontSize: 13, fontWeight: 'normal' }}
            type="clear"
            title="Đăng ảnh (Tối đa 4 hình)"
            iconRight={true}
            icon={{ name: 'camera', type: 'ionicon', size: 13, color: '#0F83FF' }}
          />
        </View>
      </View>
    );
  };

  const renderImagePickers = () => {
    return (
      <FlatList
        style={{ marginHorizontal: 15 }}
        data={imageComments}
        horizontal
        scrollEnabled={false}
        contentContainerStyle={{ alignItems: 'center' }}
        renderItem={({ item }) => (
          <FastImage source={{ uri: item.uri }} style={styles.imageUpload}>
            <Icon
              name="close"
              type="ionicon"
              color="white"
              size={10}
              containerStyle={styles.imageUploadIconContainer}
              hitSlop={{ top: 20, right: 20, bottom: 20, left: 20 }}
              onPress={() => alert(item)}
            />
          </FastImage>
        )}
      />
    );
  };

  const renderCommentUser = () => {
    return (
      <View style={{ marginTop: 15 }}>
        <Input
          editable={false}
          label="Họ và tên"
          onChangeText={setName}
          labelStyle={styles.inputLabel}
          placeholder="Nhập họ và tên"
          value={name}
        />
        <Input
          editable={false}
          label="Số điện thoại"
          onChangeText={setPhone}
          keyboardType="phone-pad"
          labelStyle={styles.inputLabel}
          placeholder="Nhập số điện thoại"
          value={phone}
        />
      </View>
    );
  };

  if (!onReady) {
    return <View style={styles.box} />;
  }

  return (
    <>
      <ScrollView style={styles.box}>
        {renderProductInfo()}
        <Divider />
        {renderFormRating()}
        {renderImagePickers()}
        {renderCommentUser()}
        <Button
          loading={isLoading}
          disabled={isLoading}
          title="Gửi đánh giá"
          type="solid"
          containerStyle={{ marginVertical: 10 }}
          buttonStyle={{ backgroundColor: '#dc0000' }}
          titleStyle={{
            fontSize: 15,
            textTransform: 'uppercase',
            fontWeight: 'normal',
          }}
          onPress={submit}
        />
        <ImagePicker
          visible={modalImagePicker}
          limitImage={4}
          onPressCamera={onUpload}
          onPressLibrary={onUpload}
          onCancel={() => setModalImagePicker(false)}
        />
      </ScrollView>
      <Animated.View style={[styles.submitedModal, containerAnimateStyle]}>
        <Image source={images.success_animate} style={styles.submitModalIcon} />
        <Text style={styles.submitModalTitle}>
          Cám ơn quý khách đã đánh giá!
        </Text>
        <Text style={styles.submitModalSubTitle}>
          Đánh giá sẽ được hiển thị sau khi ban quản trị phê duyệt
        </Text>
        <Button
          title="Quay lại trang trước"
          type="solid"
          containerStyle={{ marginTop: 20 }}
          buttonStyle={styles.submitModalButtonContainer}
          titleStyle={{
            fontSize: 15,
            textTransform: 'uppercase',
            fontWeight: 'normal',
          }}
          onPress={gobackRoute}
        />
      </Animated.View>
    </>
  );
};

const styles = StyleSheet.create({
  box: {
    flex: 1,
    backgroundColor: '#fff',
    paddingHorizontal: 10,
  },
  headerTitleStyle: {
    fontSize: 16,
    color: '#000',
    fontWeight: '500',
    flex: 1,
    marginBottom: 8,
  },
  productContainer: {
    paddingVertical: 10,
    flexDirection: 'row',
    justifyContent: 'center',
  },
  imageProduct: {
    width: 60,
    height: 60,
    resizeMode: 'contain',
    borderWidth: 1,
    borderColor: '#e3e3e3',
    borderRadius: 4,
    flex: 0,
  },
  productName: {
    fontSize: 14,
    color: '#2a2a2a',
    lineHeight: 20,
    marginLeft: 8,
    flex: 1,
    textAlignVertical: 'center',
    flexWrap: 'wrap',
  },
  rowRatingStar: {
    marginHorizontal: 5,
  },
  ratingText: {
    marginTop: 15,
    marginBottom: 10,
    fontSize: 18,
    color: '#2a2a2a',
    textAlign: 'center',
  },
  noteInputContainer: {
    marginTop: 15,
    borderWidth: 1,
    borderColor: '#e3e3e3e3',
    borderRadius: 4,
  },
  note: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  noteText: {
    fontSize: 13,
    color: '#888',
    flex: 1,
  },
  imageUpload: {
    width: 80,
    height: 80,
    position: 'relative',
    borderRadius: 4,
    resizeMode: 'contain',
    marginHorizontal: 5,
  },
  imageUploadIconContainer: {
    position: 'absolute',
    top: 2,
    right: 2,
    width: 13,
    height: 13,
    borderRadius: 13,
    backgroundColor: 'rgba(0, 0, 0, 0.75)',
    justifyContent: 'center',
    alignContent: 'center',
  },
  imageUploadFooterText: {
    margin: 6,
    textAlign: 'center',
    fontSize: 13,
    color: '#dc0000',
    lineHeight: 20,
  },
  inputLabel: {
    paddingTop: 4,
    fontSize: 14,
    color: '#2a2a2a',
    lineHeight: 12,
    fontWeight: 'normal',
  },
  submitedModal: {
    backgroundColor: 'white',
    flex: 1,
    position: 'absolute',
    top: 0,
    right: 0,
    left: 0,
    bottom: 0,
    justifyContent: 'center',
    alignItems: 'center',
  },
  submitModalIcon: {
    width: 120,
    height: 120,
    resizeMode: 'contain',
  },
  submitModalTitle: {
    color: '#0F83FF',
    fontSize: 18,
    fontWeight: '500',
    textAlign: 'center',
  },
  submitModalSubTitle: {
    color: '#555',
    fontSize: 13,
    textAlign: 'center',
  },
  submitModalButtonContainer: {
    backgroundColor: '#dc0000',
    width: 200,
    height: 40,
  },
});

export const WriteCommentScreen = Screen;
