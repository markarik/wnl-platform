import * as types from 'js/store/mutations-types';
import { get } from 'lodash';

export default (Vue, {store, router}) => {
	/**
	 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
	 * the outgoing requests issued by this application. The CSRF middleware
	 * included with Laravel will automatically verify the header's value.
	 */

	window.axios = require('axios');

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
			}

			return Promise.reject(error);
		}
	);
};
