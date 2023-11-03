import api from '@app/api';
import { ImageReponsive } from '@app/components';
import { onPressLink } from '@app/utils';
import { useRoute } from '@react-navigation/native';
import React, { useEffect } from 'react';
import { Pressable, ScrollView, TouchableOpacity, useWindowDimensions } from 'react-native';
import FastImage from 'react-native-fast-image';
import RenderHtml from 'react-native-render-html';
import { useQuery } from 'react-query';
const Screen = ({ navigation }) => {
  const route = useRoute();
  const { width } = useWindowDimensions()

  const fetch = async () => {
    return await api.getNotificationDetail(route.params?.id)
  }

  const {
    status,
    data,
    error,
    refetch,
  } = useQuery(['getDetailNotification', { id: route.params?.id }], fetch)

  useEffect(() => {
    if (data) {
      navigation.setOptions({
        title: data?.title,
      });
    }
  }, [data])


  return (
    <ScrollView style={{ padding: 10 }}>

      {data ? (
        <>
          <Pressable onPress={() => onPressLink(route.params?.link)}>
            <ImageReponsive source={{ uri: data?.images }} containerStyle={{ width: width - 20 }} />

          </Pressable>
          <RenderHtml
            source={{ html: data.content }}
            systemFonts={['SF Pro Display']}
            contentWidth={width - 20}
          />
        </>
      ) : null}
    </ScrollView>
  );
};
export const NotificationDetailScreen = Screen;
