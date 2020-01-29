
var gulp = require('gulp');
var concat = require('gulp-concat');
var projectDir = '../../../../Web/projects/autosolid/silex';

gulp.task('default', defaultTask);

function defaultTask(done) {
    // place code for your default task here
    done();
}

gulp.task('copy', copyTask);

function copyTask(done) {

    // todo: copy app files to web server dir

    gulp.src('vendor/**/*.*').pipe(gulp.dest(projectDir+'/vendor'));

    gulp.src('app/**/*.*').pipe(gulp.dest(projectDir+'/app'));

    gulp.src('views/**/*.*').pipe(gulp.dest(projectDir+'/views'));

    // todo: minify/uglify js and css
    gulp.src('public/js/bundle.js').pipe(gulp.dest(projectDir+'/public/js/'));
    gulp.src('public/css/bundle.css').pipe(gulp.dest(projectDir+'/public/css'));
    gulp.src('public/**/*.map').pipe(gulp.dest(projectDir+'/public/'));

    gulp.src(['index.php','appconfig.json','composer.json','composer.lock']).pipe(gulp.dest(projectDir));

    done();
}

gulp.task('bundle', bundleTask);

function bundleTask(done) {
    var jsFiles = [
        'public/vendors/jquery/jquery.min.js',
        'public/vendors/bootstrap4/bootstrap.min.js',
        'public/vendors/imagesloaded/imagesloaded.pkgd.min.js',
        'public/vendors/jquery.waypoints/jquery.waypoints.min.js',
        'public/vendors/jquery.countUp/jquery.countup.min.js',
        'public/vendors/jquery.matchHeight/jquery.matchHeight.min.js',
        'public/vendors/owl.carousel/owl.carousel.min.js',
        'public/vendors/menu/menu.min.js',
        'public/vendors/smoothscroll/SmoothScroll.min.js',
        'public/vendors/mark.js/jquery.mark.min.js',
        'public/js/webui.js'
    ];
    gulp.src(jsFiles)
        .pipe(concat('bundle.js'))
        .pipe(gulp.dest('public/js'));

    var cssFiles = [
        'public/vendors/bootstrap4/bootstrap-reboot.min.css',
        'public/vendors/bootstrap4/bootstrap.min.css',
        'public/vendors/bootstrap4/bootstrap-grid.min.css',
        'public/vendors/owl.carousel/owl.carousel.css',
        'public/css/main.css'
    ];
    gulp.src(cssFiles)
        .pipe(concat('bundle.css'))
        .pipe(gulp.dest('public/css'));

    done();
}

gulp.task('deploy', gulp.series(gulp.parallel('bundle', 'copy')));