import {Button, CheckBox, Divider, Header, Text} from 'react-native-elements';
import {FlatList, StyleSheet, View, TouchableOpacity} from 'react-native';
import React, {useEffect, useState, useCallback} from 'react';
import {colors, globalStyles} from '@app/assets';
import {useDispatch, useSelector} from 'react-redux';

import FastImage from 'react-native-fast-image';
import Modal from 'react-native-modal';
import {GLOBAL_FUNC, ROUTES} from '@app/constants';
import _ from 'lodash';
import api from '@app/api';
import {getTotalCart} from '@app/store/auth/services';
import {navigateRoute} from '@app/route';
import {stringHelper} from '@app/utils';

export const ModalProductBuyTogether = React.memo(
  ({onClose, visible, boughtTogether, mainProduct}) => {
    const [products, setProducts] = useState([]);
    const [isLoading, setLoading] = useState(false);
    const [listChecked, setListChecked] = useState([]);
    const dispatch = useDispatch();
    const {user, isLogin} = useSelector(state => ({
      user: state.auth.user,
      isLogin: state.auth.isLogin,
    }));
    useEffect(() => {
      if (
        boughtTogether &&
        boughtTogether.list &&
        boughtTogether.list.length > 0
      ) {
        setProducts(boughtTogether.list);
        setListChecked(boughtTogether.list.map(dt => dt.id));
      } else {
        setProducts([]);
      }
    }, [boughtTogether]);

    function renderHeader() {
      return <Text style={styles.mainHeaderText}>Mua kèm được giảm thêm</Text>;
    }

    function onToggleSelect(id) {
      const selected = _.find(listChecked, checkId => checkId === id);
      if (selected) {
        setListChecked(prev => _.filter(prev, checkId => checkId !== id));
      } else {
        setListChecked(prev => [...prev, id]);
      }
    }

    async function onPressBuy() {
      if (!isLogin) {
        onClose();
        setTimeout(() => {
          navigateRoute(ROUTES.LOGIN);
        }, 300);

        return false;
      }
      let fetchBuyTogether = [];
      if (listChecked && listChecked.length > 0) {
        fetchBuyTogether.push(api.addToCart(mainProduct.id, 1, user?.id));
        listChecked.forEach(id => {
          fetchBuyTogether.push(api.addToCart(id, 1, user?.id));
        });
      }
      if (fetchBuyTogether && fetchBuyTogether.length > 0) {
        setLoading(true);
        const res = await new Promise.all(fetchBuyTogether);
        setLoading(false);
        dispatch(getTotalCart(user?.id));
        onClose();
        setTimeout(() => {
          navigateRoute(ROUTES.CART_LIST, null, null, true);
        }, 300);
      }
    }
    const handleOnPressItem = useCallback((item) => {
      navigateRoute(
        ROUTES.DETAIL_PRODUCT,
        {...item},
        `product_detail_${item?.id}`,
      );
    }, []);
    const renderItem = ({item}) => {
      item = GLOBAL_FUNC.filterPrice(item);
      let renderPrice = null;
      if (item.price_goc) {
        renderPrice = (
          <View style={{flexDirection: 'row'}}>
            <Text style={[styles.mainProductText, {color: '#dc0000'}]}>
              {stringHelper.formatMoney(item.price)} đ
            </Text>
            <Text
              style={[
                styles.mainProductText,
                {
                  color: '#ccc',
                  textDecorationLine: 'line-through',
                  marginLeft: 8,
                },
              ]}>
              {stringHelper.formatMoney(item.price_goc)} đ
            </Text>
          </View>
        );
      }else{
        renderPrice = (
          <Text
            style={[styles.mainProductText, {color: '#dc0000', fontSize: 15}]}>
            {stringHelper.formatMoney(item.price)} đ
          </Text>
        );
      };


      // 
      // if (
      //   item.price_promotion &&
      //   stringHelper.formatToNumber(item.price_promotion) > 0
      // ) {
      //   renderPrice = (
      //     <View style={{flexDirection: 'row'}}>
      //       <Text style={[styles.mainProductText, {color: '#dc0000'}]}>
      //         {stringHelper.formatMoney(item.price_promotion)} đ
      //       </Text>
      //       <Text
      //         style={[
      //           styles.mainProductText,
      //           {
      //             color: '#ccc',
      //             textDecorationLine: 'line-through',
      //             marginLeft: 8,
      //           },
      //         ]}>
      //         {stringHelper.formatMoney(item.price_market)} đ
      //       </Text>
      //     </View>
      //   );
      // } else {
      //   renderPrice = (
      //     <Text
      //       style={[styles.mainProductText, {color: '#dc0000', fontSize: 15}]}>
      //       {stringHelper.formatMoney(item.price_promotion)} đ
      //     </Text>
      //   );
      // }
      
      return (
        <TouchableOpacity  onPress={() => handleOnPressItem(item)}
          style={[
            globalStyles.row,
            {marginVertical: 10, alignItems: 'center'},
          ]}>
          <CheckBox
            containerStyle={{paddingHorizontal: 0}}
            iconType="material-community"
            checkedIcon={'checkbox-marked'}
            uncheckedIcon={'checkbox-blank'}
            checked={_.find(listChecked, id => item.id === id)}
            onPress={() => onToggleSelect(item.id)}
          />
          <FastImage
            style={{height: 64, width: 64}}
            resizeMode="contain"
            source={{uri: item.image}}
          />
          <View style={{flex: 1, paddingHorizontal: 10}}>
            <Text style={styles.mainProductText} numberOfLines={2}>
              {item.name}
            </Text>
            {renderPrice}
          </View>
        </TouchableOpacity>
      );
    };

    const _renderPrice = () => {
      if (!mainProduct) {
        return null;
      }
      if (
        mainProduct.price_promotion &&
        stringHelper.formatToNumber(mainProduct.price_promotion) > 0
      ) {
        return (
          <>
            <Text style={[styles.mainProductText, styles.priceBefore]}>
              {`${stringHelper.formatMoney(mainProduct.price)} đ`}
            </Text>
            <Text style={[styles.mainProductText]}>
              {`${stringHelper.formatMoney(mainProduct.price_promotion)} đ`}
            </Text>
          </>
        );
      }
      return (
        <Text style={[styles.mainProductText]}>
          {`${stringHelper.formatMoney(mainProduct.price)} đ`}
        </Text>
      );
    };

    return (
      <Modal
        isVisible={visible}
        onBackButtonPress={onClose}
        onBackdropPress={onClose}
        style={styles.modalFullsize}>
        <>
          <Header
            backgroundColor={'#fff'}
            placement="left"
            rightComponent={{
              icon: 'close',
              color: '#888',
              onPress: onClose,
            }}
            centerComponent={{
              text: 'Mua kèm giảm thêm',
              style: styles.headerText,
            }}
          />
          <View style={styles.mainProductContainer}>
            <Text style={styles.mainHeaderText}>Sản phẩm chính</Text>
            <View style={[globalStyles.row, {marginVertical: 10}]}>
              <FastImage
                style={{height: 64, width: 64}}
                resizeMode="contain"
                source={{uri: mainProduct?.image}}
              />
              <View style={{flex: 1, paddingHorizontal: 10}}>
                <Text style={styles.mainProductText} numberOfLines={2}>
                  {mainProduct?.name}
                </Text>
                {_renderPrice()}
              </View>
            </View>
          </View>
          <FlatList
            ListHeaderComponent={renderHeader}
            contentContainerStyle={styles.container}
            data={products}
            renderItem={renderItem}
          />
          <View
            style={[
              globalStyles.row,
              {padding: 10, backgroundColor: '#fff', alignItems: 'center'},
            ]}>
            <View style={{flex: 1}}>
              <Text
                style={[globalStyles.text, {fontSize: 13, fontWeight: '500'}]}>
                Tổng:{' '}
                <Text style={{color: colors.primary}}>
                  {' '}
                  {stringHelper.formatMoney(boughtTogether?.total_purchase)} đ
                </Text>
              </Text>
              <Text style={[globalStyles.text, {fontSize: 12, color: '#555'}]}>
                (So với giám mua lẻ)
              </Text>
            </View>
            <Button
              loading={isLoading}
              disabled={isLoading}
              containerStyle={{flex: 1}}
              buttonStyle={{
                backgroundColor: colors.primary,
                paddingVertical: 15,
              }}
              onPress={onPressBuy}
              title={'Chọn mua'}
              titleStyle={{color: '#fff', fontSize: 17}}
            />
          </View>
        </>
      </Modal>
    );
  },
  (prev, next) => prev.visible === next.visible,
);

const styles = StyleSheet.create({
  modalFullsize: {
    margin: 0,
    padding: 0,
  },
  container: {
    flexGrow: 1,
    flex: 1,
    backgroundColor: '#fff',
    paddingHorizontal: 10,
    elevation: 2,
  },
  headerText: {
    ...globalStyles.text,
    color: '#dc0000',
    fontSize: 18,
  },
  mainProductContainer: {
    padding: 10,
    backgroundColor: '#fff',
    elevation: 2,
  },
  mainHeaderText: {
    ...globalStyles.text,
    fontSize: 15,
  },
  mainProductText: {
    ...globalStyles.text,
    fontSize: 13,
    color: '#2a2a2a',
    lineHeight: 18,
    marginBottom: 8,
  },
  priceBefore: {
    color: '#ccc',
    textDecorationLine:'line-through',
    lineHeight: 18,
    marginBottom: 8,
  },
});
