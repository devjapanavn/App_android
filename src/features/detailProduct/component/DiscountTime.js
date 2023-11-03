import { images } from '@app/assets';
import { useAppState } from '@react-native-community/hooks';
import React, { useEffect, useState } from 'react';
import { StyleSheet } from 'react-native';
import { Text } from 'react-native-elements';
import FastImage from 'react-native-fast-image';
import LinearGradient from 'react-native-linear-gradient';
import { colors, globalStyles } from '@app/assets';
import moment from 'moment';
const Component = ({ onFinish, enDate, title }) => {
  const [countDownTime, setCountDownTime] = useState({
    until: 0,
    lastUntil: null,
  });
  const [wentBackgroundAt, setBackgroundAt] = useState(null);
  const currentAppState = useAppState();
  useEffect(() => {
    if (enDate) {
      const endDayMoment = moment(enDate, 'YYYY-MM-DD HH:mm:ss');
      const duration = moment.duration(endDayMoment.diff(moment())).asSeconds();
      setCountDownTime({
        until: Math.max(parseInt(duration), 0),
        lastUntil: null,
      });
    } else {
      setCountDownTime({
        until: 0,
        lastUntil: null,
      });
    }
  }, [enDate]);

  useEffect(() => {
    const timeInverter = setInterval(updateTimer, 1000);
    return () => {
      clearInterval(timeInverter);
    };
  }, [countDownTime]);

  useEffect(() => {
    const { until } = countDownTime;
    if (currentAppState === 'active' && wentBackgroundAt) {
      const diff = (Date.now() - wentBackgroundAt) / 1000.0;
      setCountDownTime({
        until: parseInt(Math.max(0, countDownTime.until - diff), 0),
        lastUntil: until,
      });
    }
    if (currentAppState === 'background') {
      setBackgroundAt(Date.now());
    }
  }, [currentAppState]);

  function updateTimer() {
    const { lastUntil, until } = countDownTime;
    if (lastUntil === until) {
      return;
    }
    if (until === 1 || (until === 0 && lastUntil !== 1)) {
      if (onFinish) {
        onFinish();
      }
    }

    if (until === 0) {
      setCountDownTime({ lastUntil: 0, until: 0 });
    } else {
      setCountDownTime({
        until: Math.max(0, until - 1),
        lastUntil: until,
      });
    }
  }
  function getTimeLeft() {
    const { until } = countDownTime;
    return {
      seconds: until % 60,
      minutes: parseInt(until / 60, 10) % 60,
      hours: parseInt(until / (60 * 60), 10) % 24,
      days: parseInt(until / (60 * 60 * 24), 10),
    };
  }

  const { days, hours, minutes, seconds } = getTimeLeft();
  if (countDownTime && countDownTime.until > 0) {
    return (
      <>
        <Text style={styles.title}>{title}</Text>
        <LinearGradient
          start={{ x: 0, y: 0 }}
          end={{ x: 1, y: 0 }}
          colors={['#eb0800', '#FF9100']}
          style={[styles.countdown_box, styles.box]}>
          <FastImage source={images.ic_thunder} style={styles.countdown_icon} />
          <Text style={styles.countdown_text}>
            Hết hạn: {days} (ngày) {`${hours}:${minutes}:${seconds}`} (giây)
          </Text>
        </LinearGradient>
      </>
    );
  }
  return null;
};

const styles = StyleSheet.create({
  title: {
    ...globalStyles.text,
    fontSize: 12,
    color: colors.link,
  },
  countdown_box: {
    flexDirection: 'row',
    padding: 5,
    marginVertical: 9,
    alignItems: 'center',
  },
  countdown_icon: {
    width: 30,
    height: 30,
  },
  countdown_text: {
    color: '#ffffff',
    fontSize: 15,
  },
});

function areEqual(prev, next) {
  return prev.enDate === next.enDate && prev.title === next.title;
}
export const DiscountTime = React.memo(Component, areEqual);
