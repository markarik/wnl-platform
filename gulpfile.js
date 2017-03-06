var gulp = require('gulp'),
svgSprite = require('gulp-svg-sprite');

var config = {
	mode: {
		symbol: { // symbol mode to build the SVG
			dest: '', // destination folder
			sprite: 'sprite.svg' //generated sprite name
		}
	}
}

gulp.task('svg', function () {
	return gulp.src('**/*.svg', {cwd: 'resources/assets/svg'})
		.pipe(svgSprite(config))
		.pipe(gulp.dest('public/images/icons'));
});
