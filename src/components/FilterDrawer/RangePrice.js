import React, {useCallback, useEffect, useState} from 'react';
import {View} from 'react-native';
import {Text} from 'react-native-elements';
import _ from 'lodash';
import {colors} from '@app/assets';
import {stringHelper} from '@app/utils';
import {RangeSlider} from '@sharcoux/slider';

export default RangePrice = React.memo(
  ({min, max, onChangeValue}) => {
    const [rangePrice, setRangePrice] = useState({from: min, to: max});

    useEffect(() => {
      setRangePrice({from: min, to: max});
    }, [min, max]);

    const handleDebounceChangeValue = useCallback(
      _.debounce(value => {
        if (onChangeValue) {
          onChangeValue(value);
        }
      }, 400),
      [],
    );

    return (
      <View style={{padding: 10}}>
        <Text style={{fontSize: 17, marginBottom: 10, fontWeight: '500'}}>
          Giá
        </Text>
        <View style={{flexDirection: 'row', justifyContent: 'space-between'}}>
          <Text style={{fontSize: 16}}>
            {stringHelper.formatMoney(rangePrice.from)} đ
          </Text>
          <Text style={{fontSize: 16}}>
            {stringHelper.formatMoney(rangePrice.to)} đ
          </Text>
        </View>
        <View style={{marginHorizontal:20}}>
          <RangeSlider
            range={[
              stringHelper.formatToNumber(min),
              stringHelper.formatToNumber(max),
            ]}
            minimumValue={stringHelper.formatToNumber(min)}
            maximumValue={stringHelper.formatToNumber(max)}
            step={1000}
            outboundColor="grey"
            inboundColor={colors.darkPrimary}
            thumbTintColor={colors.primary}
            trackStyle={{height: 4, marginVertical: 20}}
            slideOnTap={true}
            thumbSize={20}
            onValueChange={val => {
              setRangePrice({from: val[0], to: val[1]});
              handleDebounceChangeValue({from: val[0], to: val[1]});
            }}
          />
        </View>
      </View>
    );
  },
  (prev, next) => true,
);
