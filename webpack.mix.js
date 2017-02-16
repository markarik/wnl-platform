const { mix } = require('laravel-mix');

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

// mix.js('resources/assets/js/app.js', 'public/js')
//    .sass('resources/assets/sass/app.scss', 'public/css');

mix.sass('resources/assets/sass/app.scss', 'public/css/app.css')
	.copy('resources/assets/css/reveal.css', 'public/css/reveal.css')
	.copy('resources/assets/css/reveal-theme.css', 'public/css/reveal-theme.css')
	.js('resources/assets/js/app.js', 'public/js/app.js')
	.js('resources/assets/js/payment.js', 'public/js/payment.js')
	.copy('resources/assets/js/reveal.js', 'public/js/reveal.js')