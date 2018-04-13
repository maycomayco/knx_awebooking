var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');

var notify = require('gulp-notify');
var handleErrors = function(errorObject, callback) {
  notify.onError(errorObject.toString()).apply(this, arguments);
  if (typeof this.emit === 'function') this.emit('end');
};

gulp.task('sass', function () {
  return gulp.src('sass/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass()).on('error', handleErrors)
    .pipe(autoprefixer())
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('css'))
});

gulp.task('watch', function() {
  gulp.watch(['sass/**/*'], ['sass']);
});

gulp.task('default', ['sass', 'watch']);
