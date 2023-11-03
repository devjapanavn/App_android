import api from '@app/api';
import { colors, globalStyles } from '@app/assets';
import { ROUTES } from '@app/constants';
import { navigateRoute } from '@app/route';
import { useFocusEffect, useRoute } from '@react-navigation/native';
import _ from 'lodash';
import React from 'react';
import { useEffect, useState } from 'react';
import {
  StatusBar,
  StyleSheet,
  InteractionManager,
  BackHandler,
} from 'react-native';
import { ButtonGroup } from 'react-native-elements';
import {
  useSharedValue,
} from 'react-native-reanimated';
import { SafeAreaView } from 'react-native-safe-area-context';
import { iOSColors } from 'react-native-typography';
import { useQuery } from 'react-query';
import { ListNotification } from './component';

const fetch = async () => {
  return await api.getNotificationCategory();
};
const Screen = props => {

  const route = useRoute();
  const contentOffset = useSharedValue(0);
  const [onReady, setOnReady] = useState(false);
  const [typeSelected, setTypeSelected] = useState(-1);
  const [listType, setListType] = useState([]);
  console.log('route', route.params)
  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
      setOnReady(false);
    };
  }, []);

  useFocusEffect(
    React.useCallback(() => {
      const onBackPress = () => {
        return true;
      };
      BackHandler.addEventListener('hardwareBackPress', onBackPress);

      return () =>
        BackHandler.removeEventListener('hardwareBackPress', onBackPress);
    }, []),
  );

  const { status, data, error, refetch, isLoading, isRefetching } = useQuery(
    ['getNotificationCategory'],
    () => fetch(),
    { enabled: onReady }
  );

  useEffect(() => {
    if (data && data.length > 0) {
      setListType(_.map(data, (dt) => dt.title))
      if (route.params?.id) {
        const selectedIndex = _.findIndex(data, dt => dt.id === route.params?.id_category);
        if (selectedIndex >= -1) {
          setTypeSelected(selectedIndex)
          navigateRoute(ROUTES.NOTIFICATION_DETAIL, { id: route.params?.id })
        }
      } else {
        setTypeSelected(0)
      }
    }
  }, [data])



  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#dc0000" />
      <ButtonGroup
        onPress={setTypeSelected}
        selectedIndex={typeSelected}
        buttons={listType}
        textStyle={{ fontWeight: 'bold', color: iOSColors.orange }}
        containerStyle={{ borderColor: iOSColors.orange }}
        selectedButtonStyle={{ backgroundColor: iOSColors.orange }}
        selectedTextStyle={{ color: colors.white }}

      />
      {data && data.length > 0 && typeSelected >= 0 ?
        <ListNotification categoryId={data[typeSelected].id} />
        : null}
    </SafeAreaView>
  );
};

export const NotificationScreen = Screen;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.white,
  },
  box: {
    flex: 1,
    backgroundColor: '#fff',
  },
  containerItem: {
    margin: 0,
    borderWidth: 0,
    borderBottomWidth: 1
  },
  itemTitle: {
    ...globalStyles.text,
    textAlign: 'left',
    fontSize: 15,
  },
  footerItemText: {
    ...globalStyles.text,
    color: colors.gray,
    fontSize: 12,
    marginLeft: 8
  }

});
