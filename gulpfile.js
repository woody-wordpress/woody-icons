'use strict';

const gulp = require('gulp');
const path = require('path');
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const rev = require('gulp-rev');
const sizereport = require('gulp-sizereport');
const del = require('del');
const iconfont = require('gulp-iconfont');
const iconfontCss = require('gulp-iconfont-css');
const runTimestamp = Math.round(Date.now() / 1000);
const each = require('async-each-series');
const fs = require('fs');
var glob = require('glob');

// ------------------
// Gulp Tasks
// ------------------
gulp.task('icons', done => {
    const folders = [
        'icons_set_01',
        'icons_set_02',
        'icons_set_03',
        'icons_set_04',
        'icons_set_stations',
        'shares'
    ];

    if (!fs.existsSync('./dist')) {
        fs.mkdirSync('./dist');
    }

    each(folders, function (icon_set, next) {
        gulp
            .src('./icons/' + icon_set + '/**/*.svg')
            .pipe(
                iconfontCss({
                    fontName: 'woody-icons',
                    path: './icons/template/_icons.scss.tpl',
                    targetPath: 'woody-icons.scss',
                    fontPath: './',
                    cacheBuster: runTimestamp,
                    cssClass: 'wicon'
                })
            )
            .pipe(
                iconfont({
                    fontName: 'woody-icons',
                    prependUnicode: true, // recommended option
                    formats: ['ttf', 'eot', 'woff', 'woff2', 'svg'], // default, 'woff2' and 'svg' are available
                    timestamp: runTimestamp, // recommended to get consistent builds when watching files
                    fontHeight: 1001,
                    normalize: true
                })
            )
            .pipe(gulp.dest('./dist/' + icon_set))
            .on('finish', () => {
                glob('./icons/' + icon_set + '/**/*.svg', function (err, files) {
                    var config_yml = './dist/' + icon_set + '/woody-icons.yml';
                    fs.appendFile(config_yml, 'icons:\n', (err) => {
                        if (err) throw err;
                    });

                    files.forEach((file) => {
                        var filename = path.basename(file).replace('.svg', '');
                        fs.appendFile(config_yml, '  - wicon-' + filename + '\n', (err) => {
                            if (err) throw err;
                        });
                    })
                });

                // fs.appendFile('./dist/woody-icons.scss', '@import \'./' + icon_set + '/' + icon_set + '\';\n', (err) => {
                //     if (err) throw err;
                // });

                next();
            });

    }, done);

});


gulp.task('css', () => {
    return gulp
        .src('./dist/**/*.scss')
        .pipe(
            sass({
                outputStyle: 'expanded',
                sourceMap: false,
                errLogToConsole: true
            })
        )
        .pipe(
            autoprefixer({
                overrideBrowserslist: ['last 2 versions and > 0.5%'],
                cascade: false
            })
        )
        .pipe(
            cleanCSS({
                level: 2
            })
        )
        .pipe(gulp.dest('./dist'));
});

gulp.task('clean', done => {
    del.sync('./dist/**', {
        force: true
    });
    done();
});

gulp.task('rev', () => {
    return gulp
        .src('./dist/**/*.+(css|js)')
        .pipe(rev())
        .pipe(gulp.dest('./dist')) // write rev'd assets to build dir
        .pipe(rev.manifest())
        .pipe(gulp.dest('./dist')); // write manifest to build dir
});

gulp.task('size', () => {
    return gulp.src('./dist/**/*.+(css|js)').pipe(
        sizereport({
            gzip: true
        })
    );
});

// Main tasks
gulp.task(
    'build',
    gulp.series('clean', 'icons', 'css', 'rev', 'size')
);

gulp.task('watch', () => {
    gulp.watch('./icons/**/*.*', gulp.series('build'));
});
