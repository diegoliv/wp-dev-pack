var gulp = require( 'gulp' ),
		sass = require( 'gulp-sass' ),
		autoprefixer = require( 'gulp-autoprefixer' ),
		minifycss = require( 'gulp-minify-css' ),
		jshint = require( 'gulp-jshint' ),
		uglify = require( 'gulp-uglify' ),
		imagemin = require( 'gulp-imagemin' ),
		svgmin = require( 'gulp-svgmin' ),
		rename = require( 'gulp-rename' ),
		clean = require( 'gulp-clean' ),
		concat = require( 'gulp-concat' ),
		cache = require( 'gulp-cache' ),
		header = require( 'gulp-header' ),
		footer = require( 'gulp-footer' ),
		plumber = require( 'gulp-plumber' ),
		notify = require( 'gulp-notify' ),
		iconfont = require('gulp-iconfont'),
		iconfontCss = require('gulp-iconfont-css'),
		projectTitle = '';

// styles task
gulp.task( 'styles', function() {
	return gulp.src( 'src/styles/main.scss' )
		.pipe( plumber() )
		.pipe( sass({ paths: ['src/styles/'] }) )
		.pipe( notify( {
	    title: projectTitle,
	    message: 'File: <%= file.relative %> was compiled!'
		} ) )
		.pipe( autoprefixer( 'last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4' ) )
		.pipe( gulp.dest( 'dist/assets/css' ) )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( minifycss() )
		.pipe( gulp.dest( 'dist/assets/css' ) )
		.pipe( notify( {
	    title: projectTitle,
	    message: 'Minified file: <%= file.relative %> was created / updated!'
		} ) )
} );

// styles task
gulp.task( 'editor-styles', function() {
	return gulp.src( 'src/styles/editor-styles.scss' )
		.pipe( plumber() )
		.pipe( sass({ paths: ['src/styles/'] }) )
		.pipe( notify( {
	    title: projectTitle,
	    message: 'File: <%= file.relative %> was compiled!'
		} ) )
		.pipe( autoprefixer( 'last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4' ) )
		.pipe( gulp.dest( 'dist/assets/css' ) )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( minifycss() )
		.pipe( gulp.dest( 'dist/assets/css' ) )
		.pipe( notify( {
	    title: projectTitle,
	    message: 'Minified file: <%= file.relative %> was created / updated!'
		} ) )
} );

// scripts task
gulp.task( 'scripts', function() {

	var customHeader = [
		'(function ( $ ) {',
		'"use strict";',
		'',
		'	$(function () {',
		''].join('\n');

	var customFooter = [
		'',
		'	});',
		'',
		'}(jQuery));'].join('\n');

	return gulp.src( 'src/scripts/**/*.js' )
		.pipe( plumber() )
		.pipe( jshint( '.jshintrc' ) )
		.pipe( jshint.reporter( 'default' ) )
		.pipe( concat( 'main.js' ) )
		.pipe( notify( {
	    title: projectTitle,
	    message: 'File: <%= file.relative %> was concatenated!'
		} ) )
		.pipe( header( customHeader ) )
		.pipe( footer( customFooter ) )
		.pipe( gulp.dest( 'dist/assets/js' ) )
		.pipe( rename({ suffix: '.min' }) )
		.pipe( uglify() )
		.pipe( gulp.dest( 'dist/assets/js' ) )
		.pipe( notify( {
	    title: projectTitle,
	    message: 'Minified file: <%= file.relative %> was created / updated!'
		} ) )
} );

// images task
gulp.task( 'images', function() {
	return gulp.src( 'src/images/**/*' )
		.pipe( plumber() )
		.pipe( cache( imagemin({ optimizationLevel: 7, progressive: true, interlaced: true }) ) )
		.pipe( gulp.dest( 'dist/assets/img' ) )
		.pipe( notify( {
	    title: projectTitle,
	    message: 'Image: <%= file.relative %> was optimized!'
		} ) )
} );

// svg task
gulp.task( 'svg', function() {
	return gulp.src( 'src/svg/**/*.svg' )
		.pipe( plumber() )
		.pipe( cache( svgmin() ) )
		.pipe( gulp.dest( 'dist/assets/img' ) )
		.pipe( notify( {
	    title: projectTitle,
	    message: 'SVG file: <%= file.relative %> was optimized!'
		} ) )
} );


// iconfont task
gulp.task( 'iconfont', function() {

	var fontName = 'teslacons';

	return gulp.src( ['src/icons/*.svg'] )
		.pipe( plumber() )
		.pipe( iconfontCss({
			fontName: fontName,
			path: 'src/styles/templates/_icons.scss',
			targetPath: '../../../src/styles/core/_icons.scss',
			fontPath: '../fonts/'
		}) )
		.pipe( iconfont({
			fontName: fontName,
			fixedWidth: true,
			appendCodepoints: true,
		 }) )
		.pipe( gulp.dest( 'dist/assets/fonts/' ) )
		.pipe( notify( {
	    title: projectTitle,
	    message: 'Iconfont: ' + fontName + ' was generated!'
		} ) )
} );

// clean files and folders
gulp.task( 'clean', function() {
	return gulp.src( ['dist/assets/css', 'dist/assets/js', 'dist/assets/img'], {read: false} )
		.pipe( clean() );
});

// default task
gulp.task( 'default', ['clean'], function() {
		gulp.run( 'styles', 'scripts', 'images', 'svg' )
} );

// watch task
gulp.task( 'watch', function() {

	// Watch .less files
	gulp.watch( 'src/styles/**/*.scss', [ 'styles', 'editor-styles' ] );

	// Watch .js files
	gulp.watch('src/scripts/**/*.js', ['scripts'] );

	// Watch image files
	gulp.watch('src/images/**/*', ['images'] );

	// Watch svg files
	gulp.watch( 'src/svg/**/*.svg', ['svg'] );

});