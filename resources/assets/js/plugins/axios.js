import axios from 'axios';
import {get} from 'lodash';
import {getApiUrl} from 'js/utils/env';
import * as types from 'js/store/mutations-types';

const getCsrfToken = () => {
	return axios.get(getApiUrl('token'))
		.then(response => {
			const token = get(response, 'data.token');
			if (!token) {
				throw new Error('Failed to resolve a valid CSRF token from server.');
			}
			return token;
		});

};

export default (Vue, {store, router}) => {
	/**
	 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
	 * the outgoing requests issued by this application. The CSRF middleware
	 * included with Laravel will automatically verify the header's value.
	 */

	window.axios = axios;

	window.axios.defaults.headers.common = {
		'X-CSRF-TOKEN': window.Laravel.csrfToken,
		'X-BETHINK-LOCATION': window.location.href,
		'X-Requested-With': 'XMLHttpRequest',
		'Accept': 'application/json'
	};

	window.axios.interceptors.response.use(
		(response) => Promise.resolve(response),
		(error) => {
			if (error.response && error.response.status === 403) {
				const isSuspended = !!get(error, 'response.data.account_suspended');

				if (isSuspended) {
					router.push('/');
					return store.commit(types.USERS_SET_ACCOUNT_SUSPENDED, true);
				}
			}

			if (error.response && error.response.status === 401) {
				window.location.replace('/login');
				$wnl.logger.warning('XHR resulted in 401', error.response);
			}

			if (error.config.url !== getApiUrl('token') &&
				error.config && error.response &&
				error.response.status === 419
			) {
				return getCsrfToken().then((token) => {
					let config = error.config;
					config.headers['X-CSRF-TOKEN'] = token;
					window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
					return axios.request(config);
				});
			}

			return Promise.reject(error);
		}
	);
};
