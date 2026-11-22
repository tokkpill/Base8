const { src, dest, watch, parallel } = require('gulp');

// CSS
const sass = require('gulp-sass')(require('sass'));
const plumber = require('gulp-plumber');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const postcss = require('gulp-postcss');
const sourcemaps = require('gulp-sourcemaps');

// Imagenes
const cache = require('gulp-cache');
const imagemin = require('gulp-imagemin');
const webp = require('gulp-webp');
const avif = require('gulp-avif');

// Javascript
const terser = require('gulp-terser-js');
const concat = require('gulp-concat');
const rename = require('gulp-rename')

// Webpack
const webpack = require('webpack-stream')

/**
 * Rutas de los archivos fuente
 */
const paths = {
    scss: 'src/scss/**/*.scss',
    js: 'src/js/**/*.js',
    imagenes: 'src/img/**/*'
}

/**
 * Compila los archivos SASS a CSS, aplica autoprefixer y genera sourcemaps.
 * 
 * @returns {Stream} Stream de Gulp
 */
function css() {
    return src(paths.scss)
        .pipe( sourcemaps.init())
        .pipe( sass({outputStyle: 'expanded'}))
        .pipe( postcss([autoprefixer()]))
        // .pipe( postcss([autoprefixer(), cssnano()]))
        .pipe( sourcemaps.write('.'))
        .pipe(  dest('public/build/css') );
}

/**
 * Procesa los archivos JavaScript usando Webpack, Babel y Terser para minificación.
 * 
 * @returns {Stream} Stream de Gulp
 */
function javascript() {
    return src(paths.js)
        .pipe( webpack({
            module: {
                rules: [
                    {
                        test: /\.css$/i,
                        use: ['style-loader', 'css-loader']
                    }
                ]
            },
            mode: 'production',
            watch: true,
            entry: './src/js/app.js'
        }))
        .pipe(sourcemaps.init())
        // .pipe(concat('bundle.js')) 
        .pipe(terser())
        .pipe(sourcemaps.write('.'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(dest('./public/build/js'))
}

/**
 * Optimiza y cachea las imágenes del proyecto.
 * 
 * @returns {Stream} Stream de Gulp
 */
function imagenes() {
    return src(paths.imagenes)
        .pipe( cache(imagemin({ optimizationLevel: 3})))
        .pipe( dest('public/build/img'))
}

/**
 * Crea versiones WebP de las imágenes PNG y JPG.
 * 
 * @param {function} done Callback de finalización de tarea
 */
function versionWebp( done ) {
    const opciones = {
        quality: 50
    };
    src('src/img/**/*.{png,jpg}')
        .pipe( webp(opciones) )
        .pipe( dest('public/build/img') )
    done();
}

/**
 * Crea versiones AVIF de las imágenes PNG y JPG.
 * 
 * @param {function} done Callback de finalización de tarea
 */
function versionAvif( done ) {
    const opciones = {
        quality: 50
    };
    src('src/img/**/*.{png,jpg}')
        .pipe( avif(opciones) )
        .pipe( dest('public/build/img') )
    done();
}

/**
 * Tarea de desarrollo: observa cambios en archivos SCSS, JS e imágenes.
 * 
 * @param {function} done Callback de finalización de tarea
 */
function dev(done) {
    watch( paths.scss, css );
    watch( paths.js, javascript );
    watch( paths.imagenes, imagenes)
    watch( paths.imagenes, versionWebp)
    watch( paths.imagenes, versionAvif)
    done()
}

exports.css = css;
exports.js = javascript;
exports.imagenes = imagenes;
exports.versionWebp = versionWebp;
exports.versionAvif = versionAvif;
exports.dev = parallel( css, imagenes, versionWebp, versionAvif, javascript, dev) ;