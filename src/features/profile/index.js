import {
  Avatar,
  BottomSheet,
  Button,
  Divider,
  Input,
  ListItem,
  Text,
} from 'react-native-elements';
import { CalendarModal, ImagePicker } from '@app/components';
import { Controller, useForm } from 'react-hook-form';
import {
  InteractionManager,
  ScrollView,
  StatusBar,
  StyleSheet,
  TouchableOpacity,
  View,
} from 'react-native';
import React, { useEffect, useState } from 'react';
import { convertDateStringToString, formatStringDate } from '@app/utils';
import { useDispatch, useSelector } from 'react-redux';

import DatePicker from 'react-native-date-picker';
import { SafeAreaView } from 'react-native-safe-area-context';
import api from '@app/api';
import { genderFormatString } from '@app/constants';
import { globalStyles } from '@app/assets';
import { gobackRoute } from '@app/route';
import moment from 'moment';
import { updateUser } from '@app/store/auth/reducers';

const Screen = props => {
  const { user } = useSelector(state => ({
    user: state.auth.user,
  }));
  const {
    control,
    handleSubmit,
    formState: { errors },
  } = useForm();
  const dispatch = useDispatch();
  const [modaDatePicker, setModalDatePicker] = useState(false);
  const [modalVisible, setModalVisible] = useState(false);
  const [modalImagePicker, setModalImagePicker] = useState(false);
  const [loading, setLoading] = useState(false);
  const [modalSubmit, setModalSubmit] = useState({
    image: null,
    birthday:
      user && user.birthday && user.birthday !== '0000-00-00'
        ? moment(user.birthday, 'YYYY-MM-DD')
        : moment(),
    sex: user?.sex,
  });
  const onSubmit = async data => {
    const dt = {
      id: user?.id,
      name: data['name'],
      email: data['email'],
      mobile: data['mobile'],
      sex: modalSubmit.sex,
      birthday: modalSubmit.birthday.format('YYYY-MM-DD'),
      imgs: modalSubmit.image,
    };
    try {
      setLoading(true);
      const res = await api.updateUser(dt);
      setLoading(false);
      if (res) {
        dispatch(updateUser(res));
      }
    } catch (error) {
      setLoading(false);
    }
  };

  function uploadAvatar(imgs) {
    setModalSubmit(prev => ({
      ...prev,
      image: {
        uri: imgs[0].uri,
        type: imgs[0].type,
        name: imgs[0].fileName || user?.id,
      },
    }));
  }
  function onSelectDate(date) {
    setModalSubmit(prev => ({ ...prev, birthday: moment(date) }));
    setModalDatePicker(false);
  }
  const renderFooter = () => {
    return (
      <View>
        <Divider />
        <Button
          loading={loading}
          disabled={loading}
          onPress={handleSubmit(onSubmit)}
          title="Cập nhật"
          containerStyle={styles.buttonContainer}
          titleStyle={{ fontSize: 17 }}
          buttonStyle={{ backgroundColor: '#2367ff', borderRadius: 4 }}
        />
      </View>
    );
  };

  return (
    <SafeAreaView style={styles.box}>
      <StatusBar barStyle="light-content" backgroundColor="#dc0000" />
      <ScrollView>
        <View
          style={{
            alignItems: 'center',
          }}>
          <Avatar
            size={'xlarge'}
            source={modalSubmit.image || { uri: user?.image }}
            rounded
            onPress={() => setModalImagePicker(true)}>
            <Avatar.Accessory size={23} />
          </Avatar>
        </View>
        <Controller
          control={control}
          rules={{ required: true }}
          render={({ field: { onChange, onBlur, value } }) => (
            <Input
              containerStyle={styles.inputContainer}
              label={
                <Text style={styles.inputLabelTxt}>
                  <Text style={{ color: 'red' }}>*</Text> Họ và tên
                </Text>
              }
              onBlur={onBlur}
              placeholder="Nhập họ và tên"
              placeholderTextColor="#888"
              inputStyle={styles.inputStyle}
              onChangeText={onChange}
              autoCorrect={false}
              value={value}
              errorMessage={errors.name ? 'Vui lòng nhập tên ' : null}
            />
          )}
          name="name"
          defaultValue={user?.name || ''}
        />
        <Controller
          control={control}
          rules={{ required: true }}
          render={({ field: { onChange, onBlur, value } }) => (
            <Input
              disabled={true}
              containerStyle={styles.inputContainer}
              label={
                <Text style={styles.inputLabelTxt}>
                  <Text style={{ color: 'red' }}>*</Text> Số điện thoại
                </Text>
              }
              onBlur={onBlur}
              placeholder="Nhập số điện thoại"
              placeholderTextColor="#888"
              keyboardType="phone-pad"
              inputStyle={styles.inputStyle}
              onChangeText={onChange}
              autoCorrect={false}
              value={value}
              errorMessage={errors.mobile ? 'Vui lòng số điện thoại ' : null}
            />
          )}
          name="mobile"
          defaultValue={user?.mobile || ''}
        />
        <View style={{ flexDirection: 'row', justifyContent: 'space-around' }}>
          <TouchableOpacity
            style={{ flex: 1 }}
            onPress={() => {
              setModalDatePicker(true);
            }}>
            <Input
              disabled
              containerStyle={[styles.inputContainer]}
              label={<Text style={styles.inputLabelTxt}>Ngày sinh</Text>}
              placeholder="Chọn"
              value={convertDateStringToString(modalSubmit.birthday)}
              placeholderTextColor="#888"
              inputStyle={styles.inputStyle}
              autoCorrect={false}
              rightIcon={{
                name: 'date-range',
                type: 'material',
                color: '#888',
              }}
            />
          </TouchableOpacity>
          <TouchableOpacity
            style={{ flex: 1 }}
            onPress={() => {
              setModalVisible(true);
            }}>
            <Input
              disabled
              containerStyle={[styles.inputContainer]}
              label={<Text style={styles.inputLabelTxt}>Giới tính</Text>}
              placeholder="Chọn"
              value={genderFormatString(modalSubmit.sex)}
              placeholderTextColor="#888"
              inputStyle={styles.inputStyle}
              autoCorrect={false}
              rightIcon={{
                name: 'chevron-down-outline',
                type: 'ionicon',
                color: '#888',
              }}
            />
          </TouchableOpacity>
        </View>
        <Controller
          control={control}
          render={({ field: { onChange, onBlur, value } }) => (
            <Input
              containerStyle={styles.inputContainer}
              label={<Text style={styles.inputLabelTxt}>Email</Text>}
              onBlur={onBlur}
              placeholder="Nhập email"
              placeholderTextColor="#888"
              keyboardType="email-address"
              inputStyle={styles.inputStyle}
              onChangeText={onChange}
              autoCorrect={false}
              value={value}
            />
          )}
          name="email"
          defaultValue={user?.email || ''}
        />
      </ScrollView>
      {renderFooter()}
      <ImagePicker
        visible={modalImagePicker}
        cropping={false}
        isMultiple={false}
        onPressCamera={uploadAvatar}
        onPressLibrary={uploadAvatar}
        onCancel={() => setModalImagePicker(false)}
      />
      <DatePicker
        modal
        open={modaDatePicker}
        date={modalSubmit.birthday.toDate()}
        onConfirm={onSelectDate}
        onCancel={() => {
          setModalDatePicker(false);
        }}
        mode="date"
        locale="vi"
        confirmText="Chọn"
        cancelText="Đóng"
        title="Chọn ngày sinh"
      />
      <BottomSheet isVisible={modalVisible}>
        <ListItem
          bottomDivider
          onPress={() => {
            setModalSubmit(prev => ({ ...prev, sex: '1' }));
            setModalVisible(false);
          }}>
          <ListItem.Content style={{ alignItems: 'center' }}>
            <ListItem.Title style={styles.titleStyle}>Nam</ListItem.Title>
          </ListItem.Content>
        </ListItem>
        <ListItem
          bottomDivider
          onPress={() => {
            setModalSubmit(prev => ({ ...prev, sex: '0' }));
            setModalVisible(false);
          }}>
          <ListItem.Content style={{ alignItems: 'center' }}>
            <ListItem.Title style={styles.titleStyle}>Nữ</ListItem.Title>
          </ListItem.Content>
        </ListItem>
        <ListItem bottomDivider onPress={() => setModalVisible(false)}>
          <ListItem.Content style={{ alignItems: 'center' }}>
            <ListItem.Title style={styles.titleStyle}>Đóng</ListItem.Title>
          </ListItem.Content>
        </ListItem>
      </BottomSheet>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  box: {
    flex: 1,
    backgroundColor: '#fff',
  },
  inputContainer: {
    marginVertical: 5,
  },
  inputLabelTxt: {
    fontSize: 12,
    color: '#3b4859',
  },
  inputStyle: {
    fontSize: 13,
    color: '#888',
  },
  buttonContainer: {
    margin: 10,
  },
  titleStyle: {
    ...globalStyles.text,
  },
});

export const ProfileScreen = Screen;
