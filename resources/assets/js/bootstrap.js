/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */

window.axios = require('axios');

window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN': window.Laravel.csrfToken,
	'X-Requested-With': 'XMLHttpRequest',
	'Accept': 'application/json'
};

window.axios.interceptors.response.use(null, (error) => {
	if (error.response.status === 401) {
		window.location.replace('/login');
	}
});
