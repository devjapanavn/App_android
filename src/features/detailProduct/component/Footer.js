import {appDimensions, images} from '@app/assets';
import {CONTACT_TYPE} from '@app/constants';
import {stringHelper} from '@app/utils';
import React from 'react';
import {FlatList, StyleSheet, View, Image, Linking} from 'react-native';
import {Text, Button, Tooltip, ListItem, Avatar} from 'react-native-elements';
import FastImage from 'react-native-fast-image';
import Animated from 'react-native-reanimated';
import {shallowEqual, useSelector} from 'react-redux';

const Component = ({
  onLayoutAddToCard,
  addToCart,
  statusNum,
  thumbnailImage,
}) => {
  const {hotline, social} = useSelector(
    state => ({
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
      default:
        Linking.openURL('tel:' + hotline);
        break;
    }
  }

  const _renderContact = (
    <Tooltip
      width={200}
      height={180}
      overlayColor="rgba(0,0,0,0.5)"
      popover={
        <View style={{width: 200, borderRadius: 8, overflow: 'hidden'}}>
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
      containerStyle={styles.buttonContainer}
      backgroundColor={'#fff'}>
      <Button
        TouchableComponent={View}
        buttonStyle={styles.button}
        iconPosition="top"
        icon={{
          name: 'chatbox-ellipses',
          type: 'ionicon',
          color: '#fff',
          size: 22,
        }}
        titleStyle={{fontSize: 12}}
        title="Liên hệ"
      />
    </Tooltip>
  );
  if (
    stringHelper.formatToNumber(statusNum) === 2 ||
    stringHelper.formatToNumber(statusNum) === 0
  ) {
    return (
      <View style={styles.footerContainer}>
        <View style={styles.footerLelfContainer}>
          {_renderContact}
          <Button
            onLayout={onLayoutAddToCard}
            containerStyle={styles.buttonOutOfStockContainer}
            buttonStyle={styles.button}
            title={
              stringHelper.formatToNumber(statusNum) === 0
                ? 'Hết hàng'
                : 'Tạm hết hàng'
            }
            titleStyle={{fontSize: 20, color: '#000'}}
            disabled={true}
          />
        </View>
      </View>
    );
  }
  return (
    <View style={styles.footerContainer}>
      <View style={styles.footerLelfContainer}>
        {_renderContact}
        <View style={styles.divide} />
        <View style={styles.buttonContainer}>
          <Button
            onLayout={onLayoutAddToCard}
            buttonStyle={styles.button}
            iconPosition="top"
            icon={{
              name: 'shopping-basket',
              type: 'font-awesome-5',
              color: '#fff',
              size: 22,
            }}
            title="Thêm vào giỏ"
            titleStyle={{fontSize: 12}}
            onPress={() => addToCart('ADD_TO_CART')}
          />
     
        </View>
      </View>

      <Button
        containerStyle={[styles.buttonContainer, {flex: 1}]}
        buttonStyle={[styles.button, styles.buttonBuyNow]}
        title="Mua ngay"
        titleStyle={styles.buttonBuyNowText}
        onPress={() => addToCart('BUY_NOW')}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  footerContainer: {flexDirection: 'row'},
  footerLelfContainer: {
    flexDirection: 'row',
    flex: 1,
    alignItems: 'center',
    backgroundColor: '#ffb700',
  },

  buttonOutOfStockContainer: {
    flex: 1,
    borderRadius: 0,
    backgroundColor: '#bdbdbd',
  },
  buttonContainer: {
    flex: 1,
    borderRadius: 0,
  },

  button: {
    backgroundColor: 'transparent',
    borderRadius: 0,
    height: 55,
  },
  buttonBuyNow: {
    backgroundColor: '#dc0000',
  },
  buttonBuyNowText: {
    fontSize: 17,
    textTransform: 'uppercase',
  },
  divide: {
    width: 1,
    height: 36,
    backgroundColor: '#fff',
  },
  popoverText: {
    fontSize: 15,
    fontFamily: 'SF Pro Display',
    color: '#000',
  },
});

function areEqual(prev, next) {
  return prev.statusNum === next.statusNum;
}
export const Footer = React.memo(Component, areEqual);
