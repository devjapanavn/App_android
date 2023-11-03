import React from 'react';
import { CONTACT_TYPE, ROUTES } from '@app/constants';
import { globalStyles, images } from '@app/assets';
import { resetAndNavigateRoute } from '@app/route';

import { Image, Linking, StyleSheet, TouchableOpacity, View } from 'react-native';
import {
  Avatar,
  Button,
  Icon,
  ListItem,
  Text,
  Tooltip,
} from 'react-native-elements';
import { useSelector } from 'react-redux';
import Svg, { Path } from 'react-native-svg';

export const FooterTab = React.memo(
  () => {
    const { isLogin, hotline, social } = useSelector(state => ({
      isLogin: state.auth.isLogin,
      hotline: state.root.hotline,
      social: state.root.social,
    }));
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
        default:
          Linking.openURL('tel:' + hotline);
          break;
      }
    }

    return (
      <View style={{ flexDirection: 'row' }}>
        <Button
          onPress={() => {
            if (isLogin) {
              resetAndNavigateRoute([
                { name: ROUTES.MAIN_TABS, params: { jumbTab: ROUTES.ACCOUNT } },
              ]);
            } else {
              resetAndNavigateRoute([
                { name: ROUTES.MAIN_TABS },
                { name: ROUTES.LOGIN },
              ]);
            }
          }}
          containerStyle={styles.footerButtonContainer}
          buttonStyle={styles.footerButton}
          iconPosition="top"
          icon={{ name: 'md-person-sharp', type: 'ionicon', color: '#888' }}
          title="Tài khoản"
          titleStyle={styles.footerButtonTitle}
        />
        <Button
          onPress={() => {
            resetAndNavigateRoute([{ name: ROUTES.MAIN_TABS }]);
          }}
          containerStyle={styles.footerButtonContainer}
          buttonStyle={styles.footerButton}
          iconPosition="top"
          icon={<View style={{ paddingTop:3}}>
            <Svg height={22} width={22} >
              <Path d="M 20.45 8.894 L 11.211 0.297 C 10.806 -0.08 10.194 -0.08 9.789 0.297 L 0.55 8.894 C 0.225 9.196 0.118 9.662 0.277 10.08 C 0.435 10.499 0.822 10.769 1.261 10.769 L 2.737 10.769 L 2.737 19.382 C 2.737 19.723 3.008 20 3.342 20 L 8.406 20 C 8.741 20 9.012 19.723 9.012 19.382 L 9.012 14.152 L 11.988 14.152 L 11.988 19.382 C 11.988 19.723 12.259 20 12.594 20 L 17.657 20 C 17.992 20 18.263 19.723 18.263 19.382 L 18.263 10.769 L 19.739 10.769 C 20.178 10.769 20.565 10.499 20.723 10.08 C 20.882 9.662 20.775 9.196 20.45 8.894" fill={'#888'}></Path>
            </Svg>
          </View>}
          title="Trang chủ"
          titleStyle={styles.footerButtonTitle}
        >

        </Button>
        <Button
          onPress={() => {
            resetAndNavigateRoute([
              { name: ROUTES.MAIN_TABS, params: { jumbTab: ROUTES.NOTIFICATION } },
            ])
          }}
          containerStyle={styles.footerButtonContainer}
          buttonStyle={styles.footerButton}
          iconPosition="top"
          icon={{ name: 'bell', type: 'feather', color: '#888' }}
          title="Thông báo"
          titleStyle={styles.footerButtonTitle}
        />


        <View style={[styles.footerButtonContainer, styles.footerButton]}>
          <Tooltip
            width={200}
            height={190}
            overlayColor="rgba(0,0,0,0.5)"
            popover={
              <View style={{ width: 200, borderRadius: 8, overflow: 'hidden' }}>
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
              <Image source={images.chat_bubbles} style={{ width: 22, height: 22, tintColor: '#888' }} resizeMode="contain" />
            </View>
            <Text style={styles.footerButtonTitle}>Liên hệ</Text>
          </Tooltip>
        </View>
      </View>
    );
  },
  () => true,
);
const styles = StyleSheet.create({
  footerButtonContainer: {
    flex: 1,
  },
  footerButton: {
    backgroundColor: '#fff',
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 8,
    backgroundColor: '#fff',
    borderTopColor: 'rgba(0, 0, 0, 0.2)',
    borderTopWidth: 0.5,
  },
  footerButtonTitle: {
    ...globalStyles.text,
    color: '#888',
    fontSize: 13,
  },
  popoverText: {
    fontSize: 15,
    fontFamily: 'SF Pro Display',
    color: '#000',
  },
});
