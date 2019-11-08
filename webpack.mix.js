let mix = require('laravel-mix');

let webpack = require("webpack");
mix.webpackConfig({
    plugins: [
        new webpack.ContextReplacementPlugin(/moment[\/\\]locale$/, /de/)
    ]
});

require('laravel-mix-tailwind');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .js('node_modules/popper.js/dist/popper.js', 'public/js')
    .tailwind()
    .sourceMaps()
    .version();
