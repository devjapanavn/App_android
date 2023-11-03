import React, { useEffect, useState } from 'react';
import {
    ViewPropTypes,
} from 'react-native';
import { Text } from 'react-native-elements';
import { useAppState } from '@react-native-community/hooks';
import PropTypes from 'prop-types'

const Component = ({ title, until, style, onFinish }) => {
    const [countDownTime, setCountDownTime] = useState({
        until: Math.max(until, 0),
        lastUntil: null,
    });
    const currentAppState = useAppState();
    const [wentBackgroundAt, setBackgroundAt] = useState(null);
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
                until: parseInt(Math.max(0, countDownTime.until - diff)),
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
        }
        setCountDownTime({
            until: Math.max(0, until - 1),
            lastUntil: until,
        });
    }
    return <Text style={style}>{title} {countDownTime.until}s</Text>
}


Component.propTypes = {
    until: PropTypes.number,
    onFinish: PropTypes.func,
    title: PropTypes.string,
    style: ViewPropTypes.style
};
Component.defaultProps = {
    until: 0,
    title: ""
};

export const CountDownText = React.memo(Component, (prev, next) => prev.until === next.until)