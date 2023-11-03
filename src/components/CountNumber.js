import React, {useEffect, useState, useCallback} from 'react';
import {StyleSheet, View, Text, TextInput, ViewPropTypes} from 'react-native';
import PropTypes from 'prop-types';
import {Button, Icon} from 'react-native-elements';
import {colors, spacing} from '@app/assets';
import {stringHelper} from '@app/utils';
import _ from 'lodash';
const CountNumberComponent = props => {
  const {title, value, onRemoveCart, onPress, noTitle} = props;

  const [countValue, setCountValue] = useState(value + '');
  const handleDebounceChangeValue = useCallback(
    _.debounce(value => {
      if (onPress) {
        onPress(value);
      }
    }, 400),
    [],
  );
  const increment = useCallback(() => {
    let newVal = stringHelper.formatToNumber(countValue) + 1;
    setCountValue(newVal + '');
  }, [countValue]);

  const decrement = useCallback(() => {
    let newVal = stringHelper.formatToNumber(countValue) - 1;
    if (newVal === 0) {
      if (onRemoveCart) {
        onRemoveCart();
      }
    }
    if (newVal <= 1) {
      setCountValue(1 + '');
    } else {
      setCountValue(newVal + '');
    }
  }, [countValue]);

  useEffect(() => {
    handleDebounceChangeValue(countValue);
  }, [countValue]);

  function onManualChange(val) {
    let newVal = stringHelper.formatToNumber(val);
    if (newVal <= 1) {
      setCountValue(1 + '');
    } else {
      setCountValue(newVal + '');
    }
  }
  return (
    <View style={styles.container}>
      {noTitle ? null : <Text>{title}</Text>}
      <View style={[styles.actionContainer, props.containerStyle]}>
        <Icon
          name="remove-outline"
          type="ionicon"
          color="#888888"
          size={11}
          containerStyle={[
            {
              backgroundColor: '#ededed',
            },
            styles.button,
          ]}
          hitSlop={{bottom: 15, left: 15, right: 15, top: 15}}
          onPress={decrement}
        />
        <TextInput
          style={{textAlign: 'center', color: '#000'}}
          value={countValue}
          keyboardType="numeric"
          onChangeText={onManualChange}
          selectTextOnFocus
        />
        <Icon
          name="add"
          type="ionicon"
          color="#fff"
          size={11}
          hitSlop={{bottom: 15, left: 15, right: 15, top: 15}}
          containerStyle={[
            {
              backgroundColor: '#dc0000',
            },
            styles.button,
          ]}
          onPress={increment}
        />
      </View>
    </View>
  );
};

CountNumberComponent.propTypes = {
  onPress: PropTypes.func,
  value: PropTypes.string,
  title: PropTypes.string,
  buttonColor: PropTypes.string,
  containerStyle: ViewPropTypes.style,
  noTitle: PropTypes.bool,
};
CountNumberComponent.defaultProps = {
  value: '0',
  title: 'Số lượng',
  buttonColor: colors.primary,
  noTitle: false,
};

function shouldUpdate(prevProp, nextProp) {
  return _.isEqual(prevProp.value, nextProp.value);
}
export const CountNumber = React.memo(CountNumberComponent, shouldUpdate);

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: spacing.medium,
  },
  actionContainer: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  button: {
    width: 23,
    height: 23,
    borderRadius: 4,
    justifyContent: 'center',
    alignItems: 'center',
  },
});
