const gulp      = require('gulp'),
	  svgSprite = require('gulp-svg-sprite'),
	  phpunit   = require('gulp-phpunit'),
	  notify    = require('gulp-notify');

const config = {
	mode: {
		symbol: { // symbol mode to build the SVG
			dest: '', // destination folder
			sprite: 'sprite.svg' //generated sprite name
		}
	}
}

gulp.task('svg', () => {
	return gulp.src('**/*.svg', {cwd: 'resources/assets/svg'})
		.pipe(svgSprite(config))
		.pipe(gulp.dest('public/images/icons'));
});

gulp.task('test', () => {
	const param   = process.argv[3],
		  options = {
			  debug: true,
			  statusLine: false,
			  notify: false
		  };
	gulp.src('./phpunit.xml')
		.pipe(phpunit(`./vendor/bin/phpunit ${param}`, options))
		.on('error', notify.onError({
			title: 'Dupa',
			message: 'Coś nie tak',
			// icon: __dirname + '/fail.png'
		}))
		.pipe(notify({
			title: 'OK',
			message: 'Zajebisty kod milordzie.'
		}));
});

gulp.task('watchtest', () => {
	gulp.watch(['tests/Unit/*.php', 'tests/Api/*.php', 'app/**/*.php'], ['test']);
});