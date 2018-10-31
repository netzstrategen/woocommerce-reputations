var eol = require('gulp-eol');
var eslint = require('gulp-eslint');
var gulp = require('gulp');
var runSequence = require('run-sequence');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var rename = require('gulp-rename');

gulp.task('eslint', function () {
  return gulp.src(['assets/scripts/**/*.js', '!node_modules/**', '!gulpfile.js'])
    .pipe(eslint())
    .pipe(eslint.format());
});

gulp.task('scripts', ['eslint'], function () {
  gulp.src('assets/scripts/**/*.js')
  .pipe(sourcemaps.init())
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .on('error', function(e){
      console.log(e);
    })
    .pipe(eol('\n'))
  .pipe(sourcemaps.write())
  .pipe(gulp.dest('./dist/scripts'));
});

gulp.task('build', function(callback) {
  runSequence('scripts', callback);
});

gulp.task('watch', function () {
  gulp.watch('assets/scripts/**/*.js',['scripts']);
});

gulp.task('default', function () {
  gulp.start('build');
});
