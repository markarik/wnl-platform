import Vue from 'vue'

Vue.config.productionTip = false;

window.$wnl = {
	logger: {
		warning: console.log
	}
};

// require all test files (files that ends with .spec.js)
const testsContext = require.context('../', true, /\.spec$/);
testsContext.keys().forEach(testsContext);
