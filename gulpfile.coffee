gulp = require 'gulp'

$ = require('gulp-load-plugins')({
  pattern: ['gulp-*', 'run-sequence']
});

sources =
  sass: 'assets/sass/*.scss'
  coffee: [
    'assets/coffee/*.coffee'
  ]


destinations =
  css: 'assets/css'
  js: 'assets/js'

isProd = $.util.env.type is 'prod'

###
  Compile SASS files
###
gulp.task 'style', ->
  gulp.src(sources.sass)
  .pipe($.sass({compass: true, style: 'expanded'}))
  .on('error', $.sass.logError)
  .pipe(gulp.dest(destinations.css))

###
  Run Coffee files through Coffee Lint
###
gulp.task 'lint', ->
  gulp.src(sources.coffee)
  .pipe($.coffeelint())
  .pipe($.coffeelint.reporter())

###
  Compile Coffeescript files
###
gulp.task 'coffee', ->
  if isProd
    gulp.src(sources.coffee)
    .pipe($.coffee({bare: true}).on('error', $.util.log))
    .pipe($.concat('fivestar.js'))
    .pipe($.uglify())
    .pipe(gulp.dest(destinations.js))
  else
    gulp.src(sources.coffee)
    .pipe($.coffee({bare: true}).on('error', $.util.log))
    .pipe($.concat('fivestar.js'))
    .pipe(gulp.dest(destinations.js))

###
  Keep watching files for changes to update them automatically
###
gulp.task 'watch', ->
  gulp.watch sources.sass, ['style']
  gulp.watch sources.coffee, ['lint', 'coffee']

gulp.task 'build', ['style', 'lint', 'coffee']

###
  Default command to run when calling just "gulp"
###
gulp.task 'default', ['watch']
