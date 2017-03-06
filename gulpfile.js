var gulp = require('gulp'),
svgSprite = require('gulp-svg-sprite');

var config = {
	mode: {
		symbol: { // symbol mode to build the SVG
			render: {
				css: false, // CSS output option for icon sizing
				scss: false // SCSS output option for icon sizing
			},
			dest: '', // destination folder
			prefix: '.svg--%s', // BEM-style prefix if styles rendered
			sprite: 'sprite.svg', //generated sprite name
			example: true // Build a sample page, please!
		}
	}
}

gulp.task('svg', function () {
	return gulp.src('**/*.svg', {cwd: 'resources/assets/svg'})
		.pipe(svgSprite(config))
		.pipe(gulp.dest('public/images/icons'));
});
