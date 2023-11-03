import {gobackRoute} from '@app/route';
import React, {useEffect, useState} from 'react';
import {useForm} from 'react-hook-form';
import {
  StyleSheet,
  InteractionManager,
  StatusBar,
  View,
  ScrollViewProps,
} from 'react-native';
import Animated, {
  useAnimatedScrollHandler,
  useSharedValue,
} from 'react-native-reanimated';
import {SafeAreaView} from 'react-native-safe-area-context';
import {HeaderComponent} from './Header';

export const BodyComponent = (props: ScrollViewProps) => {
  const [onReady, setOnReady] = useState(false);
  const contentOffset = useSharedValue(0);

  const handleScroll = useAnimatedScrollHandler(event => {
    contentOffset.value = event.contentOffset.y;
  });

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  return (
    <SafeAreaView style={styles.box}>
      <StatusBar barStyle="light-content" backgroundColor="#dc0000" />
      <HeaderComponent offsetY={contentOffset} showBack={props.showBack} />
      <Animated.ScrollView
        showsVerticalScrollIndicator={false}
        onScroll={handleScroll}
        {...props}>
        {onReady ? props.children : <View style={styles.box} />}
      </Animated.ScrollView>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  box: {
    flex: 1,
    backgroundColor: '#fff',
  },
});
