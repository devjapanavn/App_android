import React, {useEffect, useState} from 'react';
import {StyleSheet, InteractionManager, View} from 'react-native';
import {useSelector} from 'react-redux';
import {OrderList, OrderListStatus} from './component';
import _ from 'lodash';
import Spinner from 'react-native-spinkit';
import {colors, spacing} from '@app/assets';

const Screen = () => {
  const [onReady, setOnReady] = useState(false);
  const [selectedTab, setSelectedTab] = useState('0');


  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  function onSelectTab(tab) {
    setSelectedTab(tab);
  }

  return (
    <View style={styles.box}>
      <OrderListStatus onPress={onSelectTab} />
      {!onReady ? (
        <View
          style={{
            justifyContent: 'center',
            alignItems: 'center',
            margin: spacing.large,
            flex: 1,
          }}>
          <Spinner type="Circle" color={colors.primary} size={40} />
        </View>
      ) : (
        <OrderList  currentTab={selectedTab} />
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  box: {
    flex: 1,
    backgroundColor: '#fff',
  },
  title: {
    fontSize: 16,
  },
  buttonContainer: {
    margin: 10,
  },
});

export const OrderListScreen = Screen;
