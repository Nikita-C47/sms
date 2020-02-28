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
        'resources/js/core/jquery.3.2.1.min.js',
        'resources/js/core/popper.min.js',
        'resources/js/core/bootstrap.min.js',
        'resources/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js',
        'resources/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js',
        'resources/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js',
        'resources/js/plugin/chart.js/chart.min.js',
        'resources/js/plugin/jquery.sparkline/jquery.sparkline.min.js',
        'resources/js/plugin/chart-circle/circles.min.js',
        'resources/js/plugin/datatables/datatables.min.js',
        'resources/js/plugin/bootstrap-notify/bootstrap-notify.min.js',
        'resources/js/plugin/jqvmap/jquery.vmap.min.js',
        'resources/js/plugin/jqvmap/maps/jquery.vmap.world.js',
        'resources/js/plugin/sweetalert/sweetalert.min.js',
        'resources/js/atlantis.min.js',
        'public/js/app.js'
    ], 'public/js/app.js')
    .styles([
        'resources/css/bootstrap.min.css',
        'resources/css/atlantis.css',
        'node_modules/air-datepicker/dist/css/datepicker.min.css',
        'node_modules/@fortawesome/fontawesome-free/css/all.min.css'
    ], 'public/css/app.css')
    .copyDirectory('resources/fonts', 'public/fonts')
    .copyDirectory('resources/img', 'public/img')
    .copyDirectory('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts');

/*
mix.js('resources/js/app.js', 'public/js')
    .scripts([
        'resources/vendors/base/vendor.bundle.base.js',
        'resources/vendors/chart.js/Chart.min.js',
        'resources/js/vanilla/off-canvas.js',
        'resources/js/vanilla/hoverable-collapse.js',
        'resources/js/vanilla/template.js',
        'resources/js/vanilla/todolist.js',
        'resources/js/vanilla/dashboard.js'
    ], 'public/js/royal.js')
    .styles([
        'resources/vendors/ti-icons/css/themify-icons.css',
        'vendors/base/vendor.bundle.base.css',
        'resources/css/style.css',
        'node_modules/air-datepicker/dist/css/datepicker.min.css'
    ], 'public/css/royal.css')
    .copy('node_modules/@fortawesome/fontawesome-free/css/all.min.css', 'public/css/fa.css')
    .copyDirectory('resources/fonts', 'public/fonts')
    .copyDirectory('resources/vendors/ti-icons/fonts', 'public/fonts')
    .copyDirectory('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts');

 */
