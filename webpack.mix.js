const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .scripts([
        'resources/vendors/jquery/dist/jquery.min.js',
        'resources/vendors/popper.js/dist/umd/popper.min.js',
        'resources/vendors/bootstrap/dist/js/bootstrap.min.js',
        'resources/vendors/metisMenu/dist/metisMenu.min.js',
        'resources/vendors/jquery-slimscroll/jquery.slimscroll.min.js',
        'resources/vendors/chart.js/dist/Chart.min.js',
        'resources/vendors/jvectormap/jquery-jvectormap-2.0.3.min.js',
        'resources/vendors/jvectormap/jquery-jvectormap-world-mill-en.js',
        'resources/vendors/jvectormap/jquery-jvectormap-us-aea-en.js',
        'resources/js/admincast.js',
        'node_modules/bootstrap-notify/bootstrap-notify.js',
        'public/js/app.js'
    ], 'public/js/app.js')
    .scripts([
        'resources/vendors/jquery/dist/jquery.min.js',
        'resources/vendors/popper.js/dist/umd/popper.min.js',
        'resources/vendors/bootstrap/dist/js/bootstrap.min.js'
    ], 'public/js/auth.js')
    .scripts([
        'public/js/auth.js',
        'resources/js/admincast.js',
    ], 'public/js/error.js')
    .styles([
        'resources/vendors/bootstrap/dist/css/bootstrap.min.css',
        'resources/vendors/themify-icons/css/themify-icons.css',
        'resources/vendors/jvectormap/jquery-jvectormap-2.0.3.css',
        'resources/css/main.css',
        'resources/css/themes/white.css',
        'node_modules/air-datepicker/dist/css/datepicker.min.css',
        'node_modules/@fortawesome/fontawesome-free/css/all.min.css'
    ], 'public/css/app.css')
    .styles([
        'resources/vendors/bootstrap/dist/css/bootstrap.min.css',
        'resources/vendors/themify-icons/css/themify-icons.css',
        'node_modules/@fortawesome/fontawesome-free/css/all.min.css',
        'resources/css/main.css',
        'resources/css/pages/auth-light.css'
    ], 'public/css/auth.css')
    .styles([
        'resources/vendors/bootstrap/dist/css/bootstrap.min.css',
        'resources/css/main.css'
    ], 'public/css/error.css')
    .copyDirectory('resources/vendors/themify-icons/fonts', 'public/fonts')
    .copyDirectory('resources/img', 'public/img')
    .copyDirectory('resources/webicons', 'public')
    .copyDirectory('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts');
