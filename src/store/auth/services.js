import api from '@app/api';
import {LOCAL_STORAGE_KEY} from '@app/constants';
import {localStorage} from '@app/utils';
import {
  loginSuccess,
  logoutSuccess,
  setTotalCart,
  updateUser,
} from './reducers';

function login(username, password) {
  return dispatch => {
    return new Promise((resolve, reject) => {
      api
        .login(username, password)
        .then(async response => {
          if (response) {
            if (response.token) {
              await localStorage.save(
                LOCAL_STORAGE_KEY.USER_TOKEN,
                response.token,
              );
            }
            dispatch(loginSuccess(response));
            resolve(response);
          }
        })
        .catch(err => {
          reject(err);
        });
    });
  };
}
function loginWithOTP(phone, otp) {
  return dispatch => {
    return new Promise((resolve, reject) => {
      api
        .loginOTP(phone, otp)
        .then(async response => {
          if (response) {
            if (response.token) {
              await localStorage.save(
                LOCAL_STORAGE_KEY.USER_TOKEN,
                response.token,
              );
            }
            dispatch(loginSuccess(response));
            resolve(response);
          }
        })
        .catch(err => {
          reject(err);
        });
    });
  };
}
function onLogout(id) {
  return dispatch => {
    return new Promise((resolve, reject) => {
      api
        .logout(id)
        .then(async response => {
          console.log('response',response)
          if (response) {
            await localStorage.delete(LOCAL_STORAGE_KEY.USER_TOKEN);
            dispatch(logoutSuccess(response));
            resolve(response);
          }
        })
        .catch(err => {
          reject(err);
        });
    });
  };
}
function getProfile(userId) {
  return dispatch => {
    return new Promise((resolve, reject) => {
      api
        .getProfileUser(userId)
        .then(async response => {
          if (response) {
            dispatch(updateUser(response));
            resolve(response);
          }
        })
        .catch(err => {
          console.log('err', err);
          reject(err);
        });
    });
  };
}
function getTotalCart(userId) {
  return dispatch => {
    return new Promise((resolve, reject) => {
      api
        .getTotalItemCart(userId)
        .then(async response => {
          if (response) {
            if (response.total_item) {
              dispatch(
                setTotalCart({
                  total_item: response.total_item,
                  total: response.total,
                }),
              );
            } else {
              dispatch(
                setTotalCart({
                  total_item: 0,
                  total: 0,
                }),
              );
            }
            resolve(response);
          } else {
            dispatch(
              setTotalCart({
                total_item: 0,
                total: 0,
              }),
            );
          }
        })
        .catch(err => {
          reject(err);
        });
    });
  };
}
export {login, onLogout, loginWithOTP, getTotalCart, getProfile};
