import {images} from '@app/assets';
import React, {useCallback, useEffect, useState} from 'react';
import {StyleSheet} from 'react-native';
import {Avatar, ListItem} from 'react-native-elements';
import {useSelector, shallowEqual} from 'react-redux';

const component = () => {
  const {user} = useSelector(
    state => ({
      user: state.auth.user,
    }),
    shallowEqual,
  );

  const [avatar, setAvatar] = useState(
    user && user.image ? {uri: user?.image} : images.ic_no_avatar,
  );

  useEffect(() => {
    if (user?.image) setAvatar({uri: user?.image});
  }, [user?.image]);

  const onLoadAvataError = useCallback(() => {
    setAvatar(images.ic_no_avatar);
  }, [avatar]);
  
  return (
    <ListItem containerStyle={styles.container}>
      <Avatar
        source={avatar}
        avatarStyle={styles.avatarBorder}
        imageProps={{onError: onLoadAvataError}}
        rounded
        size="medium"
      />
      <ListItem.Content>
        <ListItem.Title style={styles.text}>{user?.name}</ListItem.Title>
        <ListItem.Subtitle style={[styles.text, {fontSize: 13}]}>
          {user?.vip?.name ? user?.vip?.name : ''}
        </ListItem.Subtitle>
      </ListItem.Content>
    </ListItem>
  );
};
export const HeaderAccount = React.memo(component, () => true);

const styles = StyleSheet.create({
  container: {backgroundColor: '#dc0000'},
  avatarBorder: {
    borderColor: '#fff',
    borderWidth: 1,
  },
  text: {
    color: '#fff',
  },
});
