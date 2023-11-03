import { images } from '@app/assets';
import { CONTACT_TYPE, ROUTES } from '@app/constants';
import React from 'react';
import { View, Text, TouchableOpacity, StyleSheet, Linking, Share } from 'react-native';
import { Avatar, ListItem, Tooltip } from 'react-native-elements';
import { shallowEqual, useSelector } from 'react-redux';

export const CustomeTabBar = ({ state, descriptors, navigation }) => {
  const { isLogin, hotline, social } = useSelector(
    state => ({
      isLogin: state.auth.isLogin,
      hotline: state.root.hotline,
      social: state.root.social,
    }),
    shallowEqual,
  );
  function openPopover(type) {
    switch (type) {
      case CONTACT_TYPE.HOTLINE:
        Linking.openURL('tel:' + hotline);
        break;
      case CONTACT_TYPE.ZALO:
        Linking.openURL(social?.zalo);
        break;
      case CONTACT_TYPE.MESSENGER:
        Linking.openURL(social?.messenger);
        break;
      case CONTACT_TYPE.SHARE:
        onShare()
        break;
      default:
        Linking.openURL('tel:' + hotline);
        break;
    }
  }
  const onShare = async () => {
    try {
      const result = await Share.share({
        message: social?.share,
      });
      if (result.action === Share.sharedAction) {
        if (result.activityType) {
          // shared with activity type of result.activityType
        } else {
          // shared
        }
      } else if (result.action === Share.dismissedAction) {
        // dismissed
      }
    } catch (error) {
      Alert.alert(error.message);
    }
  }
  return (
    <View style={{ flexDirection: 'row' }}>
      {state.routes.map((route, index) => {
        const { options } = descriptors[route.key];
        const label =
          options.tabBarLabel !== undefined
            ? options.tabBarLabel
            : options.title !== undefined
              ? options.title
              : route.name;

        const isFocused = state.index === index;

        const onPress = () => {
          const event = navigation.emit({
            type: 'tabPress',
            target: route.key,
            canPreventDefault: true,
          });

          if (!isFocused && !event.defaultPrevented) {
            // The `merge: true` option makes sure that the params inside the tab screen are preserved
            if (route.name === ROUTES.ACCOUNT && !isLogin) {
              navigation.navigate({ name: ROUTES.LOGIN, merge: true });
            } else {
              navigation.navigate({ name: route.name, merge: true });
            }
          }
        };

        const onLongPress = () => {
          navigation.emit({
            type: 'tabLongPress',
            target: route.key,
          });
        };
        const color = isFocused ? '#dc0000' : '#888';
        switch (route.name) {
          case ROUTES.CONTACT:
            return (
              <View key={route.key} style={[styles.itemContainer]}>
                <Tooltip
                  width={160}
                  height={190}
                  overlayColor="rgba(0,0,0,0.5)"
                  popover={
                    <View
                      style={{ width: 200, borderRadius: 8, overflow: 'hidden' }}>
                        <ListItem
                        bottomDivider
                        onPress={() => openPopover(CONTACT_TYPE.SHARE)}>
                        <Avatar source={images.share} size="small" />
                        <ListItem.Content>
                          <ListItem.Title style={styles.popoverText}>
                            Share
                          </ListItem.Title>
                        </ListItem.Content>
                      </ListItem>
                      <ListItem
                        bottomDivider
                        onPress={() => openPopover(CONTACT_TYPE.ZALO)}>
                        <Avatar source={images.zalo} size="small" />
                        <ListItem.Content>
                          <ListItem.Title style={styles.popoverText}>
                            Zalo chat
                          </ListItem.Title>
                        </ListItem.Content>
                      </ListItem>
                      <ListItem
                        bottomDivider
                        onPress={() => openPopover(CONTACT_TYPE.MESSENGER)}>
                        <Avatar source={images.messenger} size="small" />
                        <ListItem.Content>
                          <ListItem.Title style={styles.popoverText}>
                            Messenger
                          </ListItem.Title>
                        </ListItem.Content>
                      </ListItem>
                      <ListItem
                        bottomDivider
                        onPress={() => openPopover(CONTACT_TYPE.HOTLINE)}>
                        <Avatar source={images.callphone} size="small" />
                        <ListItem.Content>
                          <ListItem.Title style={styles.popoverText}>
                            Hotline
                          </ListItem.Title>
                        </ListItem.Content>
                      </ListItem>
                    </View>
                  }
                  backgroundColor={'#fff'}>
                  <View style={{ alignItems: 'center' }}>
                    {options.tabBarIcon({
                      focus: isFocused,
                      color: color,
                      size: 22,
                    })}
                  </View>

                  <Text style={{ color: color, paddingTop: 4 }}>{label}</Text>
                </Tooltip>
              </View>
            );

          default:
            return (
              <TouchableOpacity
                key={route.key}
                accessibilityRole="button"
                accessibilityState={isFocused ? { selected: true } : {}}
                accessibilityLabel={options.tabBarAccessibilityLabel}
                testID={options.tabBarTestID}
                onPress={onPress}
                onLongPress={onLongPress}
                style={styles.itemContainer}>
                {options.tabBarIcon({
                  focus: isFocused,
                  color: color,
                  size: 22,
                })}
                <Text style={{ color: color, paddingTop: 4 }}>{label}</Text>
              </TouchableOpacity>
            );
        }
      })}
    </View>
  );
};

const styles = StyleSheet.create({
  tooltipContainer: {
    flex: 1,
  },
  itemContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 8,
    backgroundColor: '#fff',
    borderTopColor: 'rgba(0, 0, 0, 0.2)',
    borderTopWidth: 0.5,
  },
  popoverText: {
    fontSize: 15,
    fontFamily: 'SF Pro Display',
    color: '#000',
  },
});
