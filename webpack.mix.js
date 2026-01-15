const mix = require('laravel-mix');
require('dotenv').config();

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
const glob = require('glob')

let theme = process.env.THEME || null;

// Theme color mappings (Tailwind CSS colors)
const themeColors = {
  'default': '#586cb1',
  'blue': '#6d8be6',
  'blue-light': '#62a8ea',
  // Tailwind CSS themes
  'slate': '#0f172a',
  'gray': '#171717',
  'zinc': '#18181b',
  'neutral': '#171717',
  'stone': '#1c1917',
  'red': '#dc2626',
  'orange': '#ea580c',
  'amber': '#d97706',
  'yellow': '#ca8a04',
  'lime': '#65a30d',
  'green': '#16a34a',
  'emerald': '#059669',
  'teal': '#0d9488',
  'cyan': '#0891b2',
  'sky': '#0284c7',
  'indigo': '#4f46e5',
  'violet': '#7c3aed',
  'purple': '#9333ea',
  'fuchsia': '#c026d3',
  'pink': '#db2777',
  'rose': '#e11d48'
};

// Get primary color based on theme
const primaryColor = themeColors[theme] || themeColors['default'];

let distPath = mix.inProduction() ? 'resources/dist' : 'resources/pre-dist';

function mixAssetsDir(query, cb) {
  (glob.sync('resources/assets/' + query) || []).forEach(f => {
    f = f.replace(/[\\\/]+/g, '/');
    cb(f, f.replace('resources/assets', distPath));
  });
}

function themeCss(path) {
  let sf = theme ? '-'+theme : '';

  return `${distPath}/${path}${sf}.css`
}

function dcatPath(path) {
  return 'resources/assets/dcat/' + path;
}

function dcatDistPath(path) {
  return distPath + '/dcat/' + path;
}

/*
 |--------------------------------------------------------------------------
 | Sass Configuration - Silence deprecation warnings
 |--------------------------------------------------------------------------
 */
const sassOptions = {
  quietDeps: true,
  silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'slash-div', 'legacy-js-api', 'if-function', 'abs-percent']
};

// Sass-loader options with additionalData for theme color
const sassLoaderOptions = {
  sassOptions: sassOptions,
  additionalData: `$theme-primary: ${primaryColor};`
};

/*
 |--------------------------------------------------------------------------
 | Dcat Admin assets
 |--------------------------------------------------------------------------
 */

mix.copyDirectory('resources/assets/images', distPath + '/images');
mix.copyDirectory('resources/assets/fonts', distPath + '/fonts');

// AdminLTE3.0
mix.sass('resources/assets/adminlte/scss/AdminLTE.scss', themeCss('adminlte/adminlte'), sassLoaderOptions).sourceMaps();
mix.js('resources/assets/adminlte/js/AdminLTE.js', distPath + '/adminlte/adminlte.js').sourceMaps();

// 复制第三方插件文件夹
mix.copyDirectory(dcatPath('plugins'), dcatDistPath('plugins'));
// 打包app.js
mix.js(dcatPath('js/dcat-app.js'), dcatDistPath('js/dcat-app.js')).sourceMaps();
// 打包app.scss
mix.sass(dcatPath('sass/dcat-app.scss'), themeCss('dcat/css/dcat-app'), sassLoaderOptions).sourceMaps();
mix.copy(dcatPath('sass/nunito.css'), `${distPath}/dcat/css/nunito.css`);

// 打包所有 extra 里面的所有js和css
mixAssetsDir('dcat/extra/*.js', (src, dest) => mix.js(src, dest));
mixAssetsDir('dcat/extra/*.scss', (src, dest) => mix.sass(src, dest.replace('scss', 'css'), sassLoaderOptions));

/*
 |--------------------------------------------------------------------------
 | Filament styles with prefix
 |--------------------------------------------------------------------------
 */
mix.postCss('resources/assets/filament/filament.css', distPath + '/filament/css', [
    require('@tailwindcss/postcss'),
    require('autoprefixer'),
]);
