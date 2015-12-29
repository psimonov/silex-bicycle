'use strict';

var gulp = require('gulp');

gulp.task('default', ['bower']);

var source = {
    'bootstrap/dist/css/**/*.min.css': 'css/',
    'bootstrap/dist/fonts/**/*':       'fonts/',
    'bootstrap/dist/js/**/*.min.js':   'js/',
    'jquery/dist/jquery.min.js':       'js/',
    'angular/**/*.min.js':             'js/'
};

gulp.task('bower', function() {
    console.log('╔════════════════════════════════════════════════════════════════════════');

    for (var item in source) {
        if (source.hasOwnProperty(item)) {
            var src = './bower_components/' + item;
            var dest = './web/assets/' + source[item];

            console.log('║ ' + src + ' → ' + dest);

            gulp.src(src)
                .pipe(gulp.dest(dest));
        }
    }

    console.log('╚════════════════════════════════════════════════════════════════════════');
});
