import * as React from 'react';
import {CommonActions, StackActions} from '@react-navigation/native';
import {localStorage} from '@app/utils';
import {LOCAL_STORAGE_KEY, ROUTES} from '@app/constants';
import _ from 'lodash';

export const navigationRef = React.createRef();

async function navigateRoute(name, params = null, key = null, isAuth = false) {
  if (isAuth) {
    const token = await localStorage.get(LOCAL_STORAGE_KEY.USER_TOKEN);
    console.log('token', token);
    if (_.isEmpty(token)) {
      navigationRef.current?.dispatch(
        CommonActions.navigate({
          name: ROUTES.LOGIN,
          key: key ? key : name,
        }),
      );
      return;
    }
  }
  navigationRef.current?.dispatch(
    CommonActions.navigate({
      name: name,
      params: params,
      key: key ? key : name,
    }),
  );
}

async function tabNavigate(name, params = null, key = null, isAuth = false) {
  console.log('navigationRef', navigationRef);
  navigationRef.current?.navigate(name, params);
}
async function replaceRoute(name, params = null, key = null, isAuth = false) {
  console.log('name', name);
  if (isAuth) {
    const token = await localStorage.get(LOCAL_STORAGE_KEY.USER_TOKEN);
    if (!_.isEmpty(token)) {
      navigationRef.current?.dispatch(StackActions.replace(ROUTES.LOGIN));
      return;
    }
  }
  navigationRef.current?.dispatch(StackActions.replace(name, params));
}

function resetRoute(name, params) {
  navigationRef.current?.dispatch(
    CommonActions.reset({
      index: 1,
      routes: [
        {
          name: name,
          params: params,
        },
      ],
    }),
  );
}
//
function resetAndNavigateRoute(navigates) {
  navigationRef.current?.dispatch(
    CommonActions.reset({
      index: 1,
      routes: navigates,
    }),
  );
}
function gobackRoute() {
  navigationRef.current?.dispatch(CommonActions.goBack());
}
export {
  navigateRoute,
  resetRoute,
  gobackRoute,
  replaceRoute,
  tabNavigate,
  resetAndNavigateRoute,
};
