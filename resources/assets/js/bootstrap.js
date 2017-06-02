import Echo from "laravel-echo"

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

window.io = require('socket.io-client');

window.Echo = new Echo({
	broadcaster: 'socket.io',
	host: window.location.hostname + ':1107'
});
