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

mix
	.sass('resources/assets/sass/app.scss', 'public/css/app.css')
	.js('resources/assets/js/app.js', 'public/js/app.js')
	.js('resources/assets/js/payment.js', 'public/js/payment.js')
	.js('resources/assets/js/slideshow.js', 'public/js/slideshow.js')
//
// This line was crashing HMR server, so I temporarily commented it out.
// Possibly there will be a fix for that in the next few days.
// https://github.com/JeffreyWay/laravel-mix/issues/150
//
// .copy('resources/vendor/reveal/reveal-theme.css', 'public/css/slideshow.css')

if (process.env.SYNC === 'on') {
	mix.browserSync('platforma.wnl');
}
