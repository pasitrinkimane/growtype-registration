let mix = require('laravel-mix');

mix
    .sass('resources/styles/growtype-registration.scss', 'styles')
    .sass('resources/styles/growtype-registration-login.scss', 'styles')

mix.setPublicPath('./public');
mix.setResourceRoot('./')

// mix.autoload({
//     jquery: ['$', 'window.jQuery']
// })

mix
    .js('resources/scripts/growtype-registration.js', 'scripts')

mix
    .sourceMaps()
    .version();
