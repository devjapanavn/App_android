import { createStore, applyMiddleware } from 'redux'
import thunk from 'redux-thunk'
import { createLogger } from 'redux-logger'
import AsyncStorage from '@react-native-async-storage/async-storage'
import {
  persistStore,
  persistCombineReducers
} from 'redux-persist'
import rootReducer from './root/reducers'
import authReducer from './auth/reducers'
import checkoutReducer from './checkout/reducers'
import { CONFIGS } from '@app/constants'

const rootConfig = {
  key: 'root',
  storage: AsyncStorage,
  blacklist: [''],
}
const loggerMiddleware = createLogger()
const reducer = persistCombineReducers(rootConfig, {
  root: rootReducer,
  auth: authReducer,
  checkout: checkoutReducer,
})

const store = createStore(
  reducer,
  CONFIGS.IS_DEBUG ? applyMiddleware(thunk, loggerMiddleware) : applyMiddleware(thunk)
)
persistStore(store);
export {
  store
}