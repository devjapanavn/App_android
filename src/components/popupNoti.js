import React, {useEffect, useRef, useState} from 'react';
import Modal from 'react-native-modal';
import {
  ScrollView,
  InteractionManager,
  View,
  StyleSheet,
  TouchableOpacity,
  useWindowDimensions,
} from 'react-native';
import {colors, spacing} from '@app/assets';
import _ from 'lodash';
import {RootSiblingParent} from 'react-native-root-siblings';
import {useQuery} from 'react-query';
import api from '@app/api';
import {ImageReponsive} from './imageReponsive';
import {onPressLink} from '@app/utils';
import {Icon} from 'react-native-elements';
import RenderHtml, {
  defaultHTMLElementModels,
  HTMLContentModel,
} from 'react-native-render-html';
import {useIsFocused} from '@react-navigation/native';
//1:home, 2: chi tiet sp, 3 tat ca cac trang
//type: 1 => image, type: 2 => text
const ModalPopupNoti = ({type}) => {
  const isFocus = useIsFocused();
  const [modalRenderReady, setModalRenderReady] = useState(false);
  const [modalVisible, setModalVisible] = useState(false);
  const {width, height} = useWindowDimensions();
  const timeoutRef = useRef(null);
  useEffect(() => {
    const handleScreen = InteractionManager.runAfterInteractions(() => {
      setModalRenderReady(true);
    });
    return () => {
      setModalRenderReady(false);
      clearTimeout(timeoutRef?.current);
      handleScreen.cancel();
    };
  }, []);

  const fetch = async () => {
    return await api.getPopupNotification(type);
  };

  const {status, data, error, refetch, remove} = useQuery(
    ['getPopupNotification', {type}],
    fetch,
    {
      enabled: modalRenderReady && isFocus,
      cacheTime: 0,
      staleTime: 0,
    },
  );

  useEffect(() => {
    if (!isFocus) {
      clearTimeout(timeoutRef?.current);
    }
  }, [isFocus]);

  useEffect(() => {
    if (_.some(data, _.isEmpty)) {
      timeoutRef.current = setTimeout(() => {
        if (isFocus) {
          setModalVisible(true);
        }
      }, 5000);
    }
  }, [data]);

  function onPressPopup() {
    setModalVisible(false);
    setTimeout(() => {
      onPressLink(data?.link);
    }, 300);
  }
  const customHTMLElementModels = {
    img: defaultHTMLElementModels.img.extend({
      contentModel: HTMLContentModel.mixed,
    }),
  };
  const renderBody = () => {
    if (data && data.type) {
      switch (data.type) {
        case '1':
          return (
            <View style={{position: 'relative'}}>
              <Icon
                name="md-close"
                type="ionicon"
                size={20}
                color={'#fff'}
                containerStyle={styles.closeButton}
                onPress={() => setModalVisible(false)}
              />
              <TouchableOpacity onPress={onPressPopup}>
                <ImageReponsive
                  source={{uri: data.images}}
                  style={{maxWidth: width - 100}}
                  resizeMode="contain"
                />
              </TouchableOpacity>
            </View>
          );
        case '2':
          return (
            <View style={{position: 'relative'}}>
              <Icon
                name="md-close"
                type="ionicon"
                size={20}
                color={'#fff'}
                containerStyle={styles.closeButton}
                onPress={() => setModalVisible(false)}
              />
              <TouchableOpacity onPress={onPressPopup}>
                <View>
                  <ScrollView
                    contentContainerStyle={{
                      width: width - 20,
                      maxHeight: (height * 2) / 3,
                      backgroundColor: '#fff',
                      padding: 10,
                    }}>
                    <RenderHtml
                      customHTMLElementModels={customHTMLElementModels}
                      renderersProps={{
                        img: {enableExperimentalPercentWidth: true},
                      }}
                      source={{html: data?.content}}
                      systemFonts={['SF Pro Display']}
                      contentWidth={width - 30}
                    />
                  </ScrollView>
                </View>
              </TouchableOpacity>
            </View>
          );
        default:
          break;
      }
    }
    return <View />;
  };

  if (modalRenderReady) {
    return (
      <Modal
        isVisible={modalVisible}
        onBackButtonPress={() => setModalVisible(false)}
        useNativeDriver={true}
        onBackdropPress={() => setModalVisible(false)}>
        <RootSiblingParent>{renderBody()}</RootSiblingParent>
      </Modal>
    );
  }
  return <View />;
};

function isEqual(prevProps, nextProps) {
  return prevProps.type === nextProps.type;
}
export const PopupNoti = React.memo(ModalPopupNoti, isEqual);
const styles = StyleSheet.create({
  modal_container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  header_title: {
    fontSize: 16,
    fontWeight: '700',
    color: colors.primary,
  },
  time: {
    textAlign: 'right',
    padding: spacing.small,
    fontStyle: 'italic',
    color: colors.text,
  },
  note_container: {
    marginVertical: spacing.small,
    backgroundColor: colors.white,
    borderRadius: 8,
    padding: spacing.small,
    height: 120,
    textAlignVertical: 'top',
    color: colors.text,
  },
  section_container: {
    marginHorizontal: spacing.medium,
    marginVertical: spacing.small,
  },
  closeButton: {
    position: 'absolute',
    top: -10,
    right: -10,
    backgroundColor: '#000',
    zIndex: 10,
    borderRadius: 20,
    borderColor: '#fff',
    borderWidth: 1,
    padding: 5,
  },
});
