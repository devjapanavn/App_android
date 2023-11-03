/**
 * @format
 */

import {AppRegistry, LogBox} from 'react-native';
import Root from './src/root';
import {name as appName} from './app.json';
import './wdyr';
import 'moment/locale/vi';
import moment from 'moment';
moment.locale('vi');

AppRegistry.registerComponent(appName, () => Root);
