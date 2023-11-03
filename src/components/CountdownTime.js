import React, {useEffect, useState} from 'react';
import {StyleSheet, Text, TouchableOpacity, View} from 'react-native';

import PropTypes from 'prop-types';
import _ from 'lodash';
import {useAppState} from '@react-native-community/hooks';

const DEFAULT_DIGIT_STYLE = {backgroundColor: '#FAB913'};
const DEFAULT_DIGIT_TXT_STYLE = {color: '#000'};
const DEFAULT_TIME_LABEL_STYLE = {color: '#000'};
const DEFAULT_SEPARATOR_STYLE = {color: '#000'};
const DEFAULT_TIME_TO_SHOW = ['D', 'H', 'M', 'S'];
const DEFAULT_TIME_LABELS = {
  d: 'Days',
  h: 'Hours',
  m: 'Minutes',
  s: 'Seconds',
};

const Component = props => {
  const [countDownTime, setCountDownTime] = useState({
    until: Math.max(props.until, 0),
    lastUntil: null,
  });
  const [isFinish, setIsFinish] = useState(false);
  const [wentBackgroundAt, setBackgroundAt] = useState(null);
  const currentAppState = useAppState();

  useEffect(() => {
    setIsFinish(false);
    setCountDownTime({
      until: Math.max(props.until, 0),
      lastUntil: null,
    });
  }, [props.until]);
  useEffect(() => {
    const timeInverter = setInterval(updateTimer, 1000);
    return () => {
      clearInterval(timeInverter);
    };
  }, [countDownTime]);

  useEffect(() => {
    const {until} = countDownTime;
    if (currentAppState === 'active' && wentBackgroundAt && props.running) {
      const diff = (Date.now() - wentBackgroundAt) / 1000.0;
      setCountDownTime({
        until: parseInt(Math.max(0, countDownTime.until - diff)),
        lastUntil: until,
      });
    }
    if (currentAppState === 'background') {
      setBackgroundAt(Date.now());
    }
  }, [currentAppState]);

  function updateTimer() {
    const {lastUntil, until} = countDownTime;
    if (lastUntil === until || !props.running) {
      return;
    }
    if (until === 1 || (until === 0 && lastUntil !== 1)) {
      setIsFinish(true);
      if (props.onFinish) {
        props.onFinish();
      }
      if (props.onChange) {
        props.onChange(until);
      }
    }

    if (until === 0) {
      setCountDownTime({lastUntil: 0, until: 0});
    } else {
      if (props.onChange) {
        props.onChange(until);
      }
      setCountDownTime({
        until: Math.max(0, until - 1),
        lastUntil: until,
      });
    }
  }
  function getTimeLeft() {
    const {until} = countDownTime;
    return {
      seconds: until % 60,
      minutes: parseInt(until / 60, 10) % 60,
      hours: parseInt(until / (60 * 60), 10) % 24,
      days: parseInt(until / (60 * 60 * 24), 10),
    };
  }

  function atLeast2Digit(n) {
    n = parseInt(n);
    var ret = n > 9 ? '' + n : '0' + n;
    return ret;
  }

  const renderDigit = d => {
    const {digitStyle, digitTxtStyle, size} = props;
    return (
      <View
        style={[
          styles.digitCont,
          {width: size * 3, height: size * 1.6},
          digitStyle,
        ]}>
        <Text style={[styles.digitTxt, {fontSize: size}, digitTxtStyle]}>
          {d}
        </Text>
      </View>
    );
  };
  const renderLabel = label => {
    const {timeLabelStyle, size} = props;
    if (label) {
      return (
        <Text style={[styles.timeTxt, {fontSize: size / 1.8}, timeLabelStyle]}>
          {label}
        </Text>
      );
    }
  };

  const renderDoubleDigits = (label, digits) => {
    return (
      <View style={styles.doubleDigitCont}>
        <View style={styles.timeInnerCont}>{renderDigit(digits)}</View>
        {renderLabel(label)}
      </View>
    );
  };

  const renderSeparator = () => {
    const {separatorStyle, size} = props;
    return (
      <View style={{justifyContent: 'center', alignItems: 'center'}}>
        <Text
          style={[styles.separatorTxt, {fontSize: size * 1.2}, separatorStyle]}>
          {':'}
        </Text>
      </View>
    );
  };

  const renderCountDown = () => {
    const {timeToShow, timeLabels, showSeparator} = props;
    const {days, hours, minutes, seconds} = getTimeLeft();
    const newTime = `${atLeast2Digit(days)}:${atLeast2Digit(
      hours,
    )}:${atLeast2Digit(minutes)}:${atLeast2Digit(seconds)}`.split(':');
    const Component = props.onPress ? TouchableOpacity : View;
    return (
      <Component style={styles.timeCont} onPress={props.onPress}>
        {timeToShow.includes('D')
          ? renderDoubleDigits(timeLabels.d, newTime[0])
          : null}
        {showSeparator && timeToShow.includes('D') && timeToShow.includes('H')
          ? renderSeparator()
          : null}
        {timeToShow.includes('H')
          ? renderDoubleDigits(timeLabels.h, newTime[1])
          : null}
        {showSeparator && timeToShow.includes('H') && timeToShow.includes('M')
          ? renderSeparator()
          : null}
        {timeToShow.includes('M')
          ? renderDoubleDigits(timeLabels.m, newTime[2])
          : null}
        {showSeparator && timeToShow.includes('M') && timeToShow.includes('S')
          ? renderSeparator()
          : null}
        {timeToShow.includes('S')
          ? renderDoubleDigits(timeLabels.s, newTime[3])
          : null}
      </Component>
    );
  };
  return (
    <View style={props.style}>{!isFinish ? renderCountDown() : <View />}</View>
  );
};

function areEqual(prevProps, nextProps) {
  return prevProps.until === nextProps.until;
}
export const CountdownTime = React.memo(Component, areEqual);
Component.propTypes = {
  id: PropTypes.string,
  digitStyle: PropTypes.object,
  digitTxtStyle: PropTypes.object,
  timeLabelStyle: PropTypes.object,
  separatorStyle: PropTypes.object,
  timeToShow: PropTypes.array,
  showSeparator: PropTypes.bool,
  size: PropTypes.number,
  until: PropTypes.number,
  onChange: PropTypes.func,
  onPress: PropTypes.func,
  onFinish: PropTypes.func,
};
Component.defaultProps = {
  digitStyle: DEFAULT_DIGIT_STYLE,
  digitTxtStyle: DEFAULT_DIGIT_TXT_STYLE,
  timeLabelStyle: DEFAULT_TIME_LABEL_STYLE,
  timeLabels: DEFAULT_TIME_LABELS,
  separatorStyle: DEFAULT_SEPARATOR_STYLE,
  timeToShow: DEFAULT_TIME_TO_SHOW,
  showSeparator: false,
  until: 0,
  size: 15,
  running: true,
};

const styles = StyleSheet.create({
  timeCont: {
    flexDirection: 'row',
    justifyContent: 'center',
  },
  timeTxt: {
    color: 'white',
    marginVertical: 2,
    backgroundColor: 'transparent',
  },
  timeInnerCont: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  digitCont: {
    borderRadius: 5,
    marginHorizontal: 2,
    alignItems: 'center',
    justifyContent: 'center',
  },
  doubleDigitCont: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  digitTxt: {
    color: 'white',
    fontWeight: 'bold',
    fontVariant: ['tabular-nums'],
  },
  separatorTxt: {
    backgroundColor: 'transparent',
    fontWeight: 'bold',
  },
});
