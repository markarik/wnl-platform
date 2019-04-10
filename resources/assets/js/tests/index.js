require('jsdom-global')();

const Vue = require('vue');
Vue.config.productionTip = false;

$wnl = {
	logger: {
		warning: console.log
	}
};
