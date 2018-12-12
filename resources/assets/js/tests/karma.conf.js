const webpackConfig = require('../build/webpack.test.conf');

module.exports = (config) => {
	config.set({
		browsers: ['PhantomJS'],
		frameworks: ['mocha', 'sinon-chai', 'phantomjs-shim'],
		reporters: ['spec'],
		files: [
			'../../../../node_modules/babel-polyfill/dist/polyfill.js',
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
