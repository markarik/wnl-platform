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
		if (error.response.status === 401) {
			window.location.replace('/login');
		}

		return Promise.reject(error)
	}
);

window.io = require('socket.io-client');
const Echo = require('laravel-echo');
import {envValue as env} from 'js/utils/env'

window.Echo = new Echo({
	broadcaster: 'socket.io',
	host: `${env('ECHO_HOST')}:${env('ECHO_PORT')}`,
	reconnectionDelay: 60000,
	randomizationFactor: 0.15,
	reconnectionDelayMax: 60000
});
