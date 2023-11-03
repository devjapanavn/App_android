import api from '@app/api';
import {useRoute} from '@react-navigation/native';
import React, {useEffect} from 'react';
import {ScrollView} from 'react-native';
import {useQuery} from 'react-query';
import RenderHtml from 'react-native-render-html';
import {appDimensions} from '@app/assets';
const Screen = ({navigation}) => {
  const route = useRoute();

  const fetchgetStaticBlog = async key => {
    return await api.getStaticBlog(route.params?.id);
  };

  const {status, data, error, refetch} = useQuery(
    ['getStaticBlog', {id: route.params?.id}],
    fetchgetStaticBlog,
  );

  useEffect(() => {
    if (data) {
      navigation.setOptions({
        title: data.name,
      });
    }

    return () => {};
  }, [data]);
  return (
    <ScrollView style={{padding: 10,backgroundColor:'#fff'}}>
      {data && data.desc ? (
        <RenderHtml
          source={{html: data.desc}}
          systemFonts={['SF Pro Display']}
          contentWidth={appDimensions.width - 20}
        />
      ) : null}
    </ScrollView>
  );
};
export const StaticBlog = Screen;
