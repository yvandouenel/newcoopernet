var gulp = require('gulp');
var uglify = require('gulp-uglify');
var jshint = require('gulp-jshint');
var less = require('gulp-less');
var cssmin = require('gulp-cssmin');
var concat = require('gulp-concat');
var imagemin = require('gulp-imagemin');
var rename = require('gulp-rename');
var pngcrush = require('imagemin-pngcrush');
//~ var svgstore = require('gulp-svgstore');
//~ var svgmin = require('gulp-svgmin');

// Concaténation et minification JS, en lisant la map.json
gulp.task('js', function () {
  var map = require('./js/map.json'), list = [];
  for (var i in map) {
    if (map.hasOwnProperty(i) && map[i]) {
      // Make relative to drupal path
      list.push('../../../../' + i);
    }
  }
  return gulp.src(list)
    .pipe(concat('script.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest('./dist/')).on('error', errorHandler);
});
gulp.task('jsbis', function () {
  var map = require('./js/map.json'), list = [];
  for (var i in map) {
    if (map.hasOwnProperty(i) && map[i]) {
      // Make relative to drupal path
      list.push('../../../../' + i);
    }
  }
  return gulp.src(list)
    .pipe(concat('script.full.js'))
    .pipe(gulp.dest('./dist/')).on('error', errorHandler);
});

// Vérification de la syntaxe JS
gulp.task('jshint', function () {
  var map = require('./js/map.json'), list = [];
  for (var i in map) {
    if (map.hasOwnProperty(i) && map[i] &&
        /^profiles\/labOrange/.test(i) &&
        !/\.min\.js$/.test(i) &&
        !/\/jquery\.js$/.test(i) &&
        !/\/tiny_mce\.js$/.test(i))
    {
      // Make relative to drupal path
      list.push('../../../../' + i);
    }
  }
  return gulp.src(list)
    .on('error', errorHandler)
    .pipe(jshint()).on('error', errorHandler)
    .pipe(jshint.reporter('default'))
    .pipe(jshint.reporter('fail')).on('error', errorHandler);
});

// Compilation LESS en CSS
gulp.task('less', function () {
  return gulp.src('./less/style.less')
    .pipe(less()).on('error', errorHandler)
    .pipe(cssmin({
      keepBreaks: true
    })).on('error', errorHandler)
    .pipe(rename({suffix: '.min'})).on('error', errorHandler)
    .pipe(gulp.dest('./css/')).on('error', errorHandler);
});

// Optimisation des images
gulp.task('images', function () {
  return gulp.src('./img/*')
    .pipe(imagemin({
      progressive: true,
      svgoPlugins: [{removeViewBox: false}],
      use: [pngcrush()]
    })).on('error', errorHandler)
    .pipe(gulp.dest('img')).on('error', errorHandler);
});

gulp.task('default', ['less', 'jshint', 'js', 'jsbis', 'images']);

gulp.task('watch', function () {
  //gulp.watch([
    //'./js/*.js',
    //'!node_modules/**',
    //'../../modules/**/*.js'
  //], ['js', 'jsbis', 'jshint']);
  gulp.watch('./less/**/*.less', ['less']);
  gulp.watch('./img/*', ['images']);
});

// Handle the error
function errorHandler (error) {
   console.log(error.toString());
   this.emit('end');
}
