const {mix} = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.sass('resources/assets/sass/app.scss', 'public/css/app.css')
	.sass('resources/assets/sass/slideshow.sass', 'public/css/slideshow.css')
	.sass('resources/vendor/reveal/reveal-theme.css', 'public/css/reveal.css')
	.sass('resources/vendor/emoji/emoji.css', 'public/css/emoji.css')
	.sass('resources/vendor/imageviewer/imageviewer.css', 'public/css/imageviewer.css')
	// see https://github.com/JeffreyWay/laravel-mix/issues/228#issuecomment-284076792
	// .options({
	// 	processCssUrls: false
	// })
	.js('resources/assets/js/app.js', 'public/js/app.js')
	.js('resources/assets/js/admin/admin.js', 'public/js/admin.js')
	.js('resources/assets/js/payment.js', 'public/js/payment.js')
	.js('resources/assets/js/guest.js', 'public/js/guest.js')
	.js('resources/assets/js/slideshow.js', 'public/js/slideshow.js')
	.js('resources/assets/js/notSupportedBrowserModal.js', 'public/js/notSupportedBrowserModal.js')
	.js('resources/vendor/imageviewer/imageviewer.js', 'public/js/imageviewer.js')

if (mix.config.inProduction) {
	mix.version()
}

if (process.env.SYNC === 'on') {
	mix.browserSync('platforma.wnl');
}
