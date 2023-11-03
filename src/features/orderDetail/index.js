import React, {useEffect, useState} from 'react';
import {StyleSheet, InteractionManager, ScrollView, View} from 'react-native';
import {
  stringHelper,
  convertTimeAgo,
  convertDateStringToString,
} from '@app/utils';
import {Icon, Text, colors} from 'react-native-elements';
import {orderEnum} from '@app/constants';
import styles from './styles';
import {OrderProducts} from './component';
import {useRoute} from '@react-navigation/native';
import api from '@app/api';
import {useSelector} from 'react-redux';
import {useQuery} from 'react-query';
import Spinner from 'react-native-spinkit';
import { spacing } from '@app/assets';

const fetch = async (userId, id) => {
  return await api.getDetailOrder(userId, id);
};

const Screen = () => {
  const route = useRoute();
  const [onReady, setOnReady] = useState(false);
  const {user} = useSelector(state => ({
    user: state.auth.user,
  }));

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  const {status, data, error, refetch, isLoading} = useQuery(
    ['getOrderDetail', {userId: user?.id, id: route.params?.id}],
    () => fetch(user?.id, route.params?.id)
  );
  if (isLoading) {
    return (
      <View
        style={{
          justifyContent: 'center',
          alignItems: 'center',
          margin: spacing.large,
          flex: 1,
        }}>
        <Spinner type="Circle" color={colors.primary} size={40} />
      </View>
    );
  }
  return (
    <ScrollView style={styles.container}>
      <View style={styles.box}>
        <Text style={styles.boxTitle}>Thông tin đơn</Text>
        <View style={styles.row}>
          <Icon
            name="receipt"
            type="material"
            color="rgb(138, 138, 143)"
            style={styles.icon}
          />
          <Text style={[styles.text]}>
            #{route.params?.code.toUpperCase() || '--'}{' '}
            <Text style={styles.subText}>
              (
              {convertDateStringToString(
                route.params?.date_order,
                'YYYY-MM-DD HH:mm:ss',
                'DD-MM-YYYY HH:mm',
              )}
              )
            </Text>
          </Text>
        </View>
        <View style={styles.row}>
          <Icon
            name="monetization-on"
            type="material"
            color="rgb(138, 138, 143)"
            style={styles.icon}
          />
          <Text style={[styles.text, styles.money]}>
            {stringHelper.formatMoney(data?.info?.payment?.total?.value) || 0} đ
          </Text>
        </View>
      </View>

      <View style={styles.box}>
        <Text style={styles.boxTitle}>Trạng thái</Text>
        {data &&
          data.listDateStatus &&
          data.listDateStatus.length > 0 &&
          data.listDateStatus.map((item, index) => {
            if (index === data.listDateStatus.length - 1) {
              return (
                <View style={styles.row}>
                  <Icon
                    name="adjust"
                    type="material"
                    color="rgb(43, 214, 0)"
                    style={styles.icon}
                  />
                  <Text style={styles.text}>
                    {convertDateStringToString(
                      item.date,
                      'YYYY-MM-DD HH:mm:ss',
                      'DD-MM-YYYY HH:mm',
                    )}
                  </Text>
                  <Text
                    style={[
                      styles.text,
                      {
                        fontWeight: '500',
                        color: item.status_color,
                      },
                    ]}>
                    {item.status_name}
                  </Text>
                </View>
              );
            } else {
              return (
                <View style={styles.row}>
                  <Icon
                    name="fiber-manual-record"
                    type="material"
                    color="rgb(217, 217, 217)"
                    style={styles.icon}
                  />
                  <Text style={[styles.text, {color: '#8a8a8f'}]}>
                    {convertDateStringToString(
                      item.date,
                      'YYYY-MM-DD HH:mm:ss',
                      'DD-MM-YYYY HH:mm',
                    )}
                  </Text>
                  <Text style={[styles.text, {color: '#8a8a8f'}]}>
                    {item.status_name}
                  </Text>
                </View>
              );
            }
          })}
      </View>

      <View style={styles.box}>
        <Text style={styles.boxTitle}>Thông tin giao hàng</Text>
        <View style={styles.row}>
          <Icon
            name="person"
            type="material"
            color="rgb(138, 138, 143)"
            style={styles.icon}
          />
          <Text style={[styles.text]}>
            {data?.address?.fullname} - {data?.address?.mobile}
          </Text>
        </View>
        <View style={styles.row}>
          <Icon
            name="location-on"
            type="material"
            color="rgb(138, 138, 143)"
            style={styles.icon}
          />
          <Text style={[styles.text]}>
            {stringHelper.generateFullAddress(
              data?.address?.address,
              data?.address?.ward,
              data?.address?.district,
              data?.address?.province,
            )}
          </Text>
        </View>
      </View>
      <OrderProducts products={data?.items} payments={data?.info?.payment} gift={data?.info?.gift} giftOrderAuto={data?.gift_order_auto} />
    </ScrollView>
  );
};

export const OrderDetailScreen = Screen;
