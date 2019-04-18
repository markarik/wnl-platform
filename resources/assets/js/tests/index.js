/* eslint-disable no-console */
require('jsdom-global')();

const Vue = require('vue');
Vue.config.productionTip = false;

$wnl = {
	env: {
		appUrl: 'http://test',
	},
	logger: {
		capture: console.log,
		warning: console.log
	}
};
