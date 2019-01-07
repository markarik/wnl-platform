const mix = require('laravel-mix');

const sassOptions = {
	implementation: require('node-sass')
};

mix.sass('resources/assets/sass/app.scss', 'public/css/app.css', sassOptions);

mix.js('resources/assets/js/app.js', 'public/js/app.js')
	.js('resources/assets/js/admin/admin.js', 'public/js/admin.js')
	.js('resources/assets/js/payment.js', 'public/js/payment.js')
	.js('resources/assets/js/guest.js', 'public/js/guest.js')
	.js('resources/assets/js/slideshow.js', 'public/js/slideshow.js')
	.js('resources/assets/js/notSupportedBrowserModal.js', 'public/js/notSupportedBrowserModal.js')
	.js('resources/vendor/imageviewer/imageviewer.js', 'public/js/imageviewer.js');

if (mix.config.inProduction) {
	mix.version();
}

mix.webpackConfig({
	resolve: {
		extensions: ['*', '.js', '.jsx', '.vue'],

		alias: {
			'vue$': 'vue/dist/vue.common.js',
			'js': path.resolve(__dirname, 'resources/assets/js'),
			'sass': path.resolve(__dirname, 'resources/assets/sass'),
			'vendor': path.resolve(__dirname, 'resources/vendor')
		},
	}
});
