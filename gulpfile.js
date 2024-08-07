const gulp = require("gulp");
const uglify = require("gulp-uglify");

const sass = require("gulp-sass")(require("sass"));
const cleanCSS = require("gulp-clean-css");

/**************************************************
 * COMPRESS JS
 *************************************************/
// Path configuration
const jsPaths = {
    scripts: {
        src: "resources/js/**/*.js",
        dest: "public/build/js/", // The destination for your minified JavaScript files
    },
};

// Gulp task to minify JavaScript files
gulp.task("compress", function () {
    return gulp
        .src(jsPaths.scripts.src)
        .pipe(uglify())
        .pipe(gulp.dest(jsPaths.scripts.dest));
});


/**************************************************
 * COMPRESS CSS
 *************************************************/
// Path configuration
const cssPaths = {
    styles: {
        src: "resources/scss/**/*.scss", // Adjust as needed
        dest: "public/build/css/", // Adjust as needed
    },
};
// Compiling SCSS to CSS
gulp.task("styles", function () {
    return gulp
        .src(cssPaths.styles.src)
        .pipe(sass().on("error", sass.logError)) // Compile SCSS to CSS
        .pipe(cleanCSS({ compatibility: "ie8" })) // Minify the CSS
        .pipe(gulp.dest(cssPaths.styles.dest));
});

// Watching for changes in SCSS files
gulp.task("watch", function () {
    gulp.watch(cssPaths.styles.src, gulp.series("styles"));
});
