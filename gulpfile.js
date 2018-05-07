
var gulp = require('gulp');
var run = require('gulp-run-command').default;
var projectDir = 'Web/autosolid/silex';

gulp.task('default', defaultTask);

function defaultTask(done) {
    // place code for your default task here
    done();
}

gulp.task('deploy', deployTask);

function deployTask(done) {

    // todo: copy app files to web server dir

    gulp.src('vendor/**/*.*').pipe(gulp.dest('../../../../'+projectDir+'/vendor'));

    gulp.src('app/**/*.*').pipe(gulp.dest('../../../../'+projectDir+'/app'));

    gulp.src('views/**/*.*').pipe(gulp.dest('../../../../'+projectDir+'/views'));

    // todo: minify/uglify js and css
    gulp.src('public/js/*.js').pipe(gulp.dest('../../../../'+projectDir+'/public/js'));
    gulp.src('public/css/*.css').pipe(gulp.dest('../../../../'+projectDir+'/public/css'));
    gulp.src('public/**/*.map').pipe(gulp.dest('../../../../'+projectDir+'/public/'));

    gulp.src(['index.php','appconfig.json','composer.json','composer.lock']).pipe(gulp.dest('../../../../'+projectDir));

    done();
}

gulp.task('publish', publish);

function publish(done) {
    run('phploy -l');
    //run('phploy');

    done();
}