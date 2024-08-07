const mix = require('laravel-mix');
require('laravel-mix-jigsaw');

mix.disableSuccessNotifications();
mix.setPublicPath('source/assets/build');

mix.jigsaw()
    .js([
        'source/_assets/js/main.js',
        'source/_assets/js/light-switch',
    ], 'js').vue()
    .css('source/_assets/css/main.css', 'css/main.css', [
        require('postcss-import'),
        require('tailwindcss/nesting'),
        require('tailwindcss'),
    ])
    .options({
        processCssUrls: false,
        clearConsole: false
    })
    .browserSync({
        server: 'build_local',
        files: ['build_local/**'],
        notify: false,
        open: false,
    })
    .sourceMaps()
    .version();
