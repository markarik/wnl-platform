const webpackConfig = require('../../../../node_modules/laravel-mix/setup/webpack.config.js');
delete webpackConfig.entry;

module.exports = (config) => {
	config.set({
		browsers: ['ChromeHeadless'],
		frameworks: ['mocha', 'sinon-chai'],
		reporters: ['spec'],
		files: [
			'./helpers.js',
			'./index.js'
		],
		preprocessors: {
			'./helpers.js': ['webpack'],
			'./index.js': ['webpack', 'sourcemap']
		},
		webpack: webpackConfig,
		webpackMiddleware: {
			noInfo: true
		}
	});
};
