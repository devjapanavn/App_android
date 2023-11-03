import {ROUTES} from '@app/constants';
import {navigateRoute} from '@app/route';
import {stringHelper} from '@app/utils';
import _ from 'lodash';
import React from 'react';
import {StyleSheet, TouchableOpacity, View} from 'react-native';
import {Icon, Text} from 'react-native-elements';

const component = ({data, onPress, isDefault, onEdit}) => {
  function edit() {
    if (onEdit) {
      onEdit(data);
    }
  }
  return (
    <TouchableOpacity style={styles.container} onPress={onPress}>
      <View style={styles.row}>
        <Icon
          name="person"
          type="material"
          color="rgb(138, 138, 143)"
          style={styles.icon}
        />
        <Text style={styles.text}>
          {data.fullname} - {data.mobile}
        </Text>
        <Icon
          name="edit"
          type="material"
          color="rgb(138, 138, 143)"
          style={styles.icon}
          onPress={edit}
        />
      </View>
      <View style={styles.row}>
        <Icon
          name="location-on"
          type="material"
          color="rgb(138, 138, 143)"
          style={styles.icon}
        />
        <Text style={styles.text}>
          {stringHelper.generateFullAddress(
            data.address,
            data.ward,
            data.district,
            data.province,
          )}
        </Text>
      </View>
      {isDefault === '1' ? (
        <View style={styles.row}>
          <Icon
            name="check-circle"
            type="material"
            color="rgb(43, 214, 0)"
            style={styles.icon}
          />
          <Text style={[styles.text, {color: 'rgb(43, 214, 0)'}]}>
            Mặc định
          </Text>
        </View>
      ) : null}
    </TouchableOpacity>
  );
};
export const AddressItem = React.memo(
  component,
  (prev, next) =>
    _.isEqual(prev.data, next.data) &&
    _.isEqual(prev.isDefault, next.isDefault),
);

const styles = StyleSheet.create({
  container: {
    borderWidth: 1,
    borderColor: '#d9d9d9',
    borderRadius: 8,
    marginHorizontal: 10,
    paddingVertical: 10,
    elevation: 1,
    backgroundColor: '#fff',
  },
  row: {
    flexDirection: 'row',
    padding: 5,
  },
  icon: {
    paddingHorizontal: 6,
  },
  text: {
    fontSize: 13,
    lineHeight: 22,
    flex: 1,
  },
});
