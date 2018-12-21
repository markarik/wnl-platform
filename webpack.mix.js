let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */

const sassOptions = {
	implementation: require('node-sass')
};

mix/*.sass('resources/assets/sass/app.scss', 'public/css/app.css', sassOptions)
	.sass('resources/assets/sass/slideshow.sass', 'public/css/slideshow.css', sassOptions)
	.sass('resources/vendor/reveal/reveal-theme.css', 'public/css/reveal.css', sassOptions)
	.sass('resources/vendor/emoji/emoji.css', 'public/css/emoji.css', sassOptions)
	.sass('resources/vendor/imageviewer/imageviewer.css', 'public/css/imageviewer.css', sassOptions)*/
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
	mix.version();
}

if (process.env.SYNC === 'on') {
	mix.browserSync('platforma.wnl');
}

mix.options({ extractVueStyles: true });

mix.webpackConfig({
	module: {
		rules: [
			{
				test: /\.vue$/,
				loader: 'vue-loader'
			},

			{
				test: /\.css$/,
				loaders: ['style-loader', 'css-loader']
			},

			{
				test: /\.s[ac]ss$/,
				include: /node_modules/,
				loaders: ['style-loader', 'css-loader', 'sass-loader']
			},

			{
				test: /\.html$/,
				loaders: ['html-loader']
			},

			{
				test: /\.(png|jpe?g|gif)$/,
				loader: 'file-loader',
				options: {
					name: 'images/[name].[ext]?[hash]',
				}
			},

			{
				test: /\.(woff2?|ttf|eot|svg|otf)$/,
				loader: 'file-loader',
				options: {
					name: 'fonts/[name].[ext]?[hash]',
				}
			},
		],
	},
	resolve: {
		extensions: ['*', '.js', '.jsx', '.vue'],

		alias: {
			'vue$': 'vue/dist/vue.common.js',
			'js': path.resolve(__dirname, 'resources/assets/js'),
			'vendor': path.resolve(__dirname, 'resources/vendor')
		},
	},
});

// Full API
// mix.js(src, output);
// mix.react(src, output); <-- Identical to mix.js(), but registers React Babel compilation.
// mix.preact(src, output); <-- Identical to mix.js(), but registers Preact compilation.
// mix.coffee(src, output); <-- Identical to mix.js(), but registers CoffeeScript compilation.
// mix.ts(src, output); <-- TypeScript support. Requires tsconfig.json to exist in the same folder as webpack.mix.js
// mix.extract(vendorLibs);
// mix.sass(src, output);
// mix.less(src, output);
// mix.stylus(src, output);
// mix.postCss(src, output, [require('postcss-some-plugin')()]);
// mix.browserSync('my-site.test');
// mix.combine(files, destination);
// mix.babel(files, destination); <-- Identical to mix.combine(), but also includes Babel compilation.
// mix.copy(from, to);
// mix.copyDirectory(fromDir, toDir);
// mix.minify(file);
// mix.sourceMaps(); // Enable sourcemaps
// mix.version(); // Enable versioning.
// mix.disableNotifications();
// mix.setPublicPath('path/to/public');
// mix.setResourceRoot('prefix/for/resource/locators');
// mix.autoload({}); <-- Will be passed to Webpack's ProvidePlugin.
// mix.webpackConfig({}); <-- Override webpack.config.js, without editing the file directly.
// mix.babelConfig({}); <-- Merge extra Babel configuration (plugins, etc.) with Mix's default.
// mix.then(function () {}) <-- Will be triggered each time Webpack finishes building.
// mix.extend(name, handler) <-- Extend Mix's API with your own components.
// mix.options({
//   extractVueStyles: false, // Extract .vue component styling to file, rather than inline.
//   globalVueStyles: file, // Variables file to be imported in every component.
//   processCssUrls: true, // Process/optimize relative stylesheet url()'s. Set to false, if you don't want them touched.
//   purifyCss: false, // Remove unused CSS selectors.
//   terser: {}, // Terser-specific options. https://github.com/webpack-contrib/terser-webpack-plugin#options
//   postCss: [] // Post-CSS options: https://github.com/postcss/postcss/blob/master/docs/plugins.md
// });
