import api from '@app/api';
import { getVersion } from './reducers';

function getAppInfoService() {
	return dispatch => {
		return new Promise((resolve, reject) => {
			api.getAppInfo()
				.then(response => {
					if (response) {
						dispatch(getVersion(response));
						resolve(response);
					}
				})
				.catch(err => {
					reject(err);
				});
		});
	};
}

export { getAppInfoService };
