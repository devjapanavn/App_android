import {DefaultTheme, NavigationContainer} from '@react-navigation/native';
import {Icon, ThemeProvider} from 'react-native-elements';
import {LogBox, StyleSheet} from 'react-native';
import {QueryClient, QueryClientProvider} from 'react-query';
import React, {Component} from 'react';
import {RootSiblingParent, setSiblingWrapper} from 'react-native-root-siblings';

import App from './features/app';
import FlashMessage from 'react-native-flash-message';
import {GestureHandlerRootView} from 'react-native-gesture-handler';
import {MenuProvider} from 'react-native-popup-menu';
import {Provider} from 'react-redux';
import {ROUTES} from './constants';
import {SafeAreaProvider} from 'react-native-safe-area-context';
import {View} from 'react-native';
import {colors} from './assets';
import {createStackNavigator} from '@react-navigation/stack';
import {navigationRef} from './route';
import { store } from './store';

const RootStack = createStackNavigator();
const queryClient = new QueryClient();
LogBox.ignoreLogs(['Setting a timer']);

setSiblingWrapper(sibling => <Provider store={store}>{sibling}</Provider>);

const appTheme = {
  ...DefaultTheme,
  colors: {
    ...DefaultTheme.colors,
    primary: colors.primary,
    background: colors.black,
  },
};
const elementTheme = {
  Text: {
    style: {fontFamily: 'SF Pro Display'},
  },
  Button: {
    titleStyle: {fontFamily: 'SF Pro Display'},
  },
  Chip: {
    titleStyle: {fontFamily: 'SF Pro Display'},
  },
};
export default class Root extends Component {
  constructor(props) {
    super(props);
  }

  componentDidMount() {}

  componentWillUnmount() {}

  renderFlashMessageIcon(icon = 'success', style = {}, customProps = {}) {
    switch (icon) {
      case 'chat': // casting for your custom icons and render then
        return (
          <View style={styles.flastMessContainer}>
            <Icon
              name="chatbubble-ellipses-outline"
              type="ionicon"
              size={22}
              color="white"
            />
          </View>
        );
      default:
        // if not a custom icon render the default ones...
        return renderFlashMessageIcon(icon, style, customProps);
    }
  }

  render() {
    return (
      <RootSiblingParent>
        <GestureHandlerRootView style={{flex: 1}}>
          <Provider store={store}>
            <QueryClientProvider client={queryClient}>
              <MenuProvider>
                <SafeAreaProvider>
                  <ThemeProvider theme={elementTheme}>
                    <NavigationContainer
                      ref={navigationRef}
                      theme={{dark: true, colors: appTheme}}>
                      <RootStack.Navigator screenOptions={{headerShown: false}}>
                        <RootStack.Screen name={ROUTES.APP} component={App} />
                      </RootStack.Navigator>
                    </NavigationContainer>
                    <FlashMessage
                      position="top"
                      renderFlashMessageIcon={this.renderFlashMessageIcon.bind(
                        this,
                      )}
                    />
                  </ThemeProvider>
                </SafeAreaProvider>
              </MenuProvider>
            </QueryClientProvider>
          </Provider>
        </GestureHandlerRootView>
      </RootSiblingParent>
    );
  }
}

const styles = StyleSheet.create({
  flastMessContainer: {
    marginTop: -1,
    marginRight: 7,
    width: 21, // thats the recomended size of icons
    height: 21, // thats the recomended size of icons
  },
});
