import React, { } from 'react';
import { StyleSheet, View, Dimensions } from "react-native";
import Animated, { runOnJS, useAnimatedGestureHandler, useAnimatedStyle, useSharedValue } from 'react-native-reanimated'
import { PanGestureHandler, State } from "react-native-gesture-handler";
import Icon from "react-native-vector-icons/MaterialCommunityIcons";

const { width } = Dimensions.get("window");

const SLIDER_WIDTH = width - 150;
const RULER_HEIGHT = 10;
const KNOB_SIZE = 20;
const MIN = 0;
const MAX = 1000;

const Component = ({ onChangeValue }) => {
  const translationX_From = useSharedValue(0)
  const translationX_To = useSharedValue(0)

  const _onPanGestureEventFrom = useAnimatedGestureHandler({
    onStart: (evt, ctx) => {
      ctx.offsetX = translationX_From.value;
    },
    onActive: (evt, ctx) => {
      const finalX = evt.translationX + (ctx.offsetX || 0);
      if (finalX < 0) {
        translationX_From.value = 0;
      } else if (finalX >= SLIDER_WIDTH - Math.abs(translationX_To.value) - KNOB_SIZE * 2.5) {
        translationX_From.value = SLIDER_WIDTH - Math.abs(translationX_To.value) - KNOB_SIZE * 2.5;
      }
      else {
        translationX_From.value = evt.translationX + (ctx.offsetX || 0);
      }
    },
    onEnd: () => {
      runOnJS(onChange)();
    }
  })


  const _onPanGestureEventTo = useAnimatedGestureHandler({
    onStart: (evt, ctx) => {
      ctx.offsetX = translationX_To.value;
    },
    onActive: (evt, ctx) => {
      const finalX = evt.translationX + (ctx.offsetX || 0);
      if (finalX > 0) {
        translationX_To.value = 0;
      } else if (Math.abs(finalX) >= SLIDER_WIDTH - translationX_From.value - KNOB_SIZE * 2.5) {
        translationX_To.value = (SLIDER_WIDTH - translationX_From.value - KNOB_SIZE * 2.5) * -1;
      } else {
        translationX_To.value = evt.translationX + (ctx.offsetX || 0);
      }
    },
    onEnd: () => {
      runOnJS(onChange)();
    }
  })





  const animateButtonFrom = useAnimatedStyle(() => {
    return {
      transform: [{ translateX: translationX_From.value }],
      position: "absolute",
      top: 0,
      left: 0,
      width: KNOB_SIZE,
      height: KNOB_SIZE,
      backgroundColor: 'red',
    };
  });

  const animateButtonTo = useAnimatedStyle(() => {
    return {
      transform: [{ translateX: translationX_To.value }],
      position: "absolute",
      top: 0,
      right: 0,
      width: KNOB_SIZE,
      height: KNOB_SIZE,
      backgroundColor: 'blue',
    };
  });

  const activeBar = useAnimatedStyle(() => {
    return {
      width: SLIDER_WIDTH - Math.abs(translationX_To.value) - translationX_From.value,
      transform: [{ translateX: translationX_From.value }]
    }
  })

  function onChange() {
    const valueFrom = Math.abs(translationX_From.value) * MAX / SLIDER_WIDTH
    const valueTo = (SLIDER_WIDTH - Math.abs(translationX_To.value)) * MAX / SLIDER_WIDTH
    console.log('onChange _ valueFrom', valueFrom);
    console.log('onChange _ valueTo', valueTo);

  }
  // ----------------- Render ----------------------- //
  return (
    <View style={styles.container}>
      <View style={styles.slider}>
        <View>
          <View style={styles.backgroundSlider} />
          <Animated.View
            style={[styles.activeSlide, activeBar]}
          />
        </View>
        <PanGestureHandler activeOffsetX={[-10, 10]} onGestureEvent={_onPanGestureEventFrom} >
          <Animated.View
            style={animateButtonFrom}
          >
            <Animated.View
              style={{
                ...StyleSheet.absoluteFillObject,
                // transform: [{ rotate }],
              }}
            >
              <Icon name='close' />
            </Animated.View>
          </Animated.View>
        </PanGestureHandler>
        <PanGestureHandler activeOffsetX={[-10, 10]} minDist={0} hitSlop={10} onGestureEvent={_onPanGestureEventTo} >
          <Animated.View
            style={animateButtonTo}
          >
            <Animated.View
              style={{
                ...StyleSheet.absoluteFillObject,
                // transform: [{ rotate }],
              }}
            >
              <Icon name='close' />
            </Animated.View>
          </Animated.View>
        </PanGestureHandler>
      </View>
    </View>
  );
};

function areEqual(prev, next) {
  return false;
}
export const RangeSlider = React.memo(Component, areEqual);

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#a9cbee",
    justifyContent: "center",
    alignItems: "center",
  },
  slider: {
    width: SLIDER_WIDTH,
    height: KNOB_SIZE,
    justifyContent: "center",
  },
  backgroundSlider: {
    height: RULER_HEIGHT,
    backgroundColor: "white",
  },
  activeSlide: {
    ...StyleSheet.absoluteFillObject,
    height: RULER_HEIGHT,
    backgroundColor: 'green',
  },
  sides: {
    ...StyleSheet.absoluteFillObject,
    flexDirection: "row",
    justifyContent: "space-between",
  },
  left: {
    height: RULER_HEIGHT,
    width: RULER_HEIGHT,
    borderRadius: RULER_HEIGHT / 2,
    backgroundColor: 'red',
    left: -RULER_HEIGHT / 2,
  },
  right: {
    left: RULER_HEIGHT / 2,
    height: RULER_HEIGHT,
    width: RULER_HEIGHT,
    borderRadius: RULER_HEIGHT / 2,
    backgroundColor: "white",
  },
});
