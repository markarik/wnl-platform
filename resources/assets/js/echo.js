window.io = require('socket.io-client');
const Echo = require('laravel-echo');
import {envValue as env} from 'js/utils/env'

window.Echo = new Echo({
	broadcaster: 'socket.io',
	host: `${env('ECHO_HOST')}:${env('ECHO_PORT')}`,
	reconnectionDelay: 60000,
	randomizationFactor: 0.15,
	reconnectionDelayMax: 120000
});
