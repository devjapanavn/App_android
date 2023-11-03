import {CONFIGS, LOCAL_STORAGE_KEY} from '@app/constants';
import {toastAlert} from './toastAlert';
import {localStorage} from './localstorage';
import _ from 'lodash';
const HEADERS = async requireToken => {
  const baseHeader = {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  };
  if (requireToken) {
    const token = await localStorage.get(LOCAL_STORAGE_KEY.USER_TOKEN);
    baseHeader.Authorization = token;
  }
  return baseHeader;
};

const serializeFormData = function (data) {
  if (_.isEmpty(data)) {
    return null;
  }
  let body = new FormData();
  Object.keys(data).map(function (keyName) {
    if (keyName === 'images[]') {
      if (Array.isArray(data[keyName])) {
        data[keyName].forEach(element => {
          body.append(keyName, element);
        });
      }
    } else {
      body.append(keyName, data[keyName]);
    }
  });
  return body;
};
export function FETCH({
  url = CONFIGS.REST_API_ROOT_URL,
  path,
  method = 'POST',
  headers = {},
  body,
  requireToken = false,
}) {
  const fetchURL = `${url}${path}`;
  switch (method) {
    case 'GET':
      return new Promise(function (resolve, reject) {
        startGet(fetchURL, method, headers)
          .then(response => {
            resolve(response);
          })
          .catch(error => reject(error));
      });
    default:
      return new Promise(function (resolve, reject) {
        startFetch(fetchURL, method, headers, body, requireToken)
          .then(response => {
            resolve(response);
          })
          .catch(error => reject(error));
      });
  }
}

function startFetch(path, method, headers, dataBody, requireToken) {
  return new Promise(async (resolve, reject) => {
    const timeout = setTimeout(() => {
      reject(new Error('Request API timeout.'));
    }, 30000);

    const formBody = serializeFormData({
      ...dataBody,
    });
    console.log('formBody', formBody);
    console.log('path', path);
    const xhr = new XMLHttpRequest();
    xhr.open(method, path);
    if (requireToken) {
      const token = await localStorage.get(LOCAL_STORAGE_KEY.USER_TOKEN);
      console.log('Authorization', token);
      xhr.setRequestHeader('Authorization', token);
    }
    if (formBody) {
      xhr.send(formBody);
    } else {
      xhr.send();
    }
    xhr.onreadystatechange = e => {
      if (xhr.readyState === 4) {
        return;
      }
      if (xhr.status === 200) {
        if (xhr.responseText) {
          try {
            const dataRes = JSON.parse(xhr.responseText);
            if (dataRes) {
              if (dataRes.status === 'success' || dataRes.status === 'succes') {
                console.log(path, dataRes.data);
                resolve(dataRes.data);
              } else {
                toastAlert(dataRes.message);
                reject({error: dataRes.code, message: dataRes.message});
              }
            }
          } catch (error) {
            console.log('catch error', error.message);
            reject(error);
          }
        }
      } else {
        console.log('xhr.responseText', xhr.responseText);
        reject(xhr.responseText);
      }
    };
  });
}
