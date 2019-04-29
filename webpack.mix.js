const mix = require('laravel-mix');

const sassOptions = {
	implementation: require('node-sass')
};

mix.sass('resources/assets/sass/app.scss', 'public/css/app.css', sassOptions)
	.sass('resources/vendor/reveal/reveal-theme-bodywork.scss', 'public/css/reveal-theme-bodywork.css', sassOptions)
	.sass('resources/vendor/reveal/reveal-theme-wnl.scss', 'public/css/reveal-theme-wnl.css', sassOptions)
	.sass('resources/vendor/emoji/emoji.scss', 'public/css/emoji.css', sassOptions);

mix.js('resources/assets/js/app.js', 'public/js/app.js')
	.js('resources/assets/js/admin/admin.js', 'public/js/admin.js')
	.js('resources/assets/js/payment.js', 'public/js/payment.js')
	.js('resources/assets/js/guest.js', 'public/js/guest.js')
	.js('resources/assets/js/slideshow.js', 'public/js/slideshow.js')
	.js('resources/assets/js/notSupportedBrowserModal.js', 'public/js/notSupportedBrowserModal.js')
	.js('resources/vendor/imageviewer/imageviewer.js', 'public/js/imageviewer.js');

mix.copyDirectory('resources/assets/images', 'public/images');
mix.copyDirectory('resources/assets/svg', 'public/svg');

mix.options({
	hmrStyleLoaderOptions: {
		insertAt: {
			before: 'style'
		}
	}
});

if (mix.inProduction()) {
	mix.version();
}

const webpackConfig = {
	resolve: {
		extensions: ['*', '.js', '.jsx', '.vue'],

		alias: {
			'vue$': 'vue/dist/vue.common.js',
			'js': path.resolve(__dirname, 'resources/assets/js'),
			'sass': path.resolve(__dirname, 'resources/assets/sass'),
			'vendor': path.resolve(__dirname, 'resources/vendor')
		},
	},
};

if (process.env.NODE_ENV === 'testing') {
	// See https://github.com/zinserjan/mocha-webpack/blob/master/docs/installation/webpack-configuration.md
	webpackConfig.externals = [require('webpack-node-externals')()];
}

mix.webpackConfig(webpackConfig);
