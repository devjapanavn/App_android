import { globalStyles } from '@app/assets';
import { orderEnum, ROUTES } from '@app/constants';
import { navigateRoute } from '@app/route';
import { stringHelper } from '@app/utils';
import React, { useCallback } from 'react';
import { StyleSheet, TouchableOpacity, View } from 'react-native';
import { Icon, Text } from 'react-native-elements';

const component = ({ data }) => {
  const handleOnPress = useCallback(() => {
    navigateRoute(ROUTES.ORDER_DETAIL, data, null, true);
  }, []);
  return (
    <TouchableOpacity style={styles.container} onPress={handleOnPress}>
      <View style={styles.row}>
        <Icon
          name="receipt"
          type="material"
          color="rgb(138, 138, 143)"
          style={styles.icon}
        />
        <Text style={[styles.text, styles.code]}>#{data.code}  - ({data?.date_order})</Text>
      </View>
      <View style={styles.row}>
        <Icon
          name="monetization-on"
          type="material"
          color="rgb(138, 138, 143)"
          style={styles.icon}
        />
        <Text style={styles.text}>{stringHelper.formatMoney(data.total)}</Text>
      </View>
      <View style={styles.row}>
        <Icon
          name="layers"
          type="material"
          color="rgb(138, 138, 143)"
          style={styles.icon}
        />
        <Text style={styles.text} numberOfLines={2}>
          {data.item?.name_vi}
        </Text>
      </View>
      <View style={styles.row}>
        <Icon
          name="sync-circle"
          type="ionicon"
          color="rgb(138, 138, 143)"
          style={styles.icon}
        />
        <Text
          style={[
            styles.text,
            { color: data.status_color },
          ]}>
          {data.status_name}
        </Text>
        <Text style={[{ color: '#2367ff', fontSize: 13, paddingHorizontal: 10 }]}>
          Chi tiết
        </Text>
      </View>
    </TouchableOpacity>
  );
};
export const OrderItem = React.memo(component, () => true);

const styles = StyleSheet.create({
  container: {
    borderWidth: 1,
    borderColor: '#d9d9d9',
    borderRadius: 8,
    marginHorizontal: 10,
    paddingVertical: 8,
    elevation: 1,
    backgroundColor: '#fff',
  },
  row: {
    flexDirection: 'row',
    justifyContent: 'center',
    padding: 5,
  },
  icon: {
    paddingHorizontal: 6,
  },
  text: {
    ...globalStyles.text,
    fontSize: 13,
    lineHeight: 22,
    flex: 1,
  },
  code: {
    textTransform: 'uppercase',
  },
});
