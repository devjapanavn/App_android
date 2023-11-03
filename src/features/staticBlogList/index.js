import api from '@app/api';
import {ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import React, {useEffect} from 'react';
import {FlatList, StyleSheet, TouchableOpacity} from 'react-native';
import {Avatar, ListItem} from 'react-native-elements';
import {useQuery} from 'react-query';
const Screen = ({navigation}) => {
  const fetchListStaticBlog = async key => {
    return await api.getListStaticBlog();
  };

  const {status, data, error, refetch} = useQuery(
    ['getListStaticBlog'],
    fetchListStaticBlog,
  );

  return (
    <FlatList
      data={data || []}
      removeClippedSubviews={true}
      keyExtractor={item => `static_blog_${item.id}`}
      contentContainerStyle={{backgroundColor:'#fff'}}
      renderItem={({item, index}) => (
        <ListItem
          Component={TouchableOpacity}
          containerStyle={styles.item}
          onPress={() => navigateRoute(ROUTES.STATIC_BLOG, {id: item.id})}>
          <Avatar
            rounded
            source={{uri: item.images}}
          />
          <ListItem.Content>
            <ListItem.Title style={styles.title}>{item.name}</ListItem.Title>
          </ListItem.Content>
          <ListItem.Chevron
            name="chevron-forward"
            type="ionicon"
            color="rgb(138, 138, 143)"
          />
        </ListItem>
      )}
    />
  );
};
export const ListStaticBlog = Screen;
const styles = StyleSheet.create({
  item: {
    margin: 10,
    padding: 10,
    elevation: 2,
    borderRadius: 5,
  },
});
