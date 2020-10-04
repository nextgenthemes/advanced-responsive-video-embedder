/* eslint-disable @typescript-eslint/no-var-requires */
/* eslint-disable no-console */
const mix = require('laravel-mix');
const ImageminPlugin = require('imagemin-webpack-plugin').default;
const CopyWebpackPlugin = require('copy-webpack-plugin');
const imageminMozjpeg = require('imagemin-mozjpeg');
const BabelImportJsxPragma = require('@wordpress/babel-plugin-import-jsx-pragma');
const BabelTransformReactJsx = require('@babel/plugin-transform-react-jsx');

mix.sourceMaps();
mix.setPublicPath('dist');

mix.ts('src/ts/main.ts', 'js');
mix.ts('src/ts/admin.ts', 'js');
mix.ts('src/ts/gb-block.ts', 'js');
mix.ts('src/ts/test-block.ts', 'js');
mix.ts('src/ts/shortcode-ui.ts', 'js');

mix.ts('src/common/ts/settings.ts', 'common/js');
mix.ts('src/common/ts/notice-ajax.ts', 'common/js');

mix.sass('src/scss/main.scss', 'css');
mix.sass('src/scss/admin.scss', 'css');

mix.sass('src/common/scss/settings.scss', 'common/css');

if (process.env.sync) {
	mix.browserSync({
		proxy: 'symbiosisthemes.test/arve/',
		files: [
			'dist/**/*',
			'src/views/**/*.php',
			'app/**/*.php',
			'php/**/*.php',
			'*.php',
		],
	});
}

mix.webpackConfig({
	stats: 'minimal',
	devtool: mix.inProduction() ? false : 'source-map',
	performance: { hints: false },
	plugins: [
		new BabelImportJsxPragma({
			scopeVariable: 'createElement',
			scopeVariableFrag: 'Fragment',
			source: '@wordpress/element',
			isDefault: false,
		}),
		new BabelTransformReactJsx({
			pragma: 'createElement',
			pragmaFrag: 'Fragment',
		}),
		// @link https://github.com/webpack-contrib/copy-webpack-plugin
		new CopyWebpackPlugin({
			patterns: [
				{ from: 'src/img', to: 'img' },
				{ from: 'src/svg', to: 'svg' },
			],
		}),
		// @link https://github.com/Klathmon/imagemin-webpack-plugin
		new ImageminPlugin({
			test: /\.(jpe?g|png|gif|svg)$/i,
			disable: process.env.NODE_ENV !== 'production',
			optipng: { optimizationLevel: 3 },
			gifsicle: { optimizationLevel: 3 },
			pngquant: {
				quality: '65-90',
				speed: 4,
			},
			svgo: {
				plugins: [
					{ cleanupIDs: false },
					{ removeViewBox: false },
					{ removeUnknownsAndDefaults: false },
				],
			},
			plugins: [
				// @link https://github.com/imagemin/imagemin-mozjpeg
				imageminMozjpeg({ quality: 75 }),
			],
		}),
	],
});

// Full API
// mix.js(src, output);
// mix.react(src, output); <-- Identical to mix.js(), but registers React Babel compilation.
// mix.preact(src, output); <-- Identical to mix.js(), but registers Preact compilation.
// mix.coffee(src, output); <-- Identical to mix.js(), but registers CoffeeScript compilation.
// mix.ts(src, output); <-- TypeScript support. Requires tsconfig.json to exist in the same folder as webpack.mix.js
// mix.extract(vendorLibs);
// mix.sass(src, output);
// mix.standaloneSass('src', output); <-- Faster, but isolated from Webpack.
// mix.fastSass('src', output); <-- Alias for mix.standaloneSass().
// mix.less(src, output);
// mix.stylus(src, output);
// mix.postCss(src, output, [require('postcss-some-plugin')()]);
// mix.browserSync('my-site.test');
// mix.combine(files, destination);
// mix.babel(files, destination); <-- Identical to mix.combine(), but also includes Babel compilation.
// mix.copy(from, to);
// mix.copyDirectory(fromDir, toDir);
// mix.minify(file);
// mix.sourceMaps(); // Enable sourcemaps
// mix.version(); // Enable versioning.
// mix.disableNotifications();
// mix.setPublicPath('path/to/public');
// mix.setResourceRoot('prefix/for/resource/locators');
// mix.autoload({}); <-- Will be passed to Webpack's ProvidePlugin.
// mix.webpackConfig({}); <-- Override webpack.config.js, without editing the file directly.
// mix.babelConfig({}); <-- Merge extra Babel configuration (plugins, etc.) with Mix's default.
// mix.then(function () {}) <-- Will be triggered each time Webpack finishes building.
// mix.extend(name, handler) <-- Extend Mix's API with your own components.
// mix.options({
//   extractVueStyles: false, // Extract .vue component styling to file, rather than inline.
//   globalVueStyles: file, // Variables file to be imported in every component.
//   processCssUrls: true, // Process/optimize relative stylesheet url()'s. Set to false, if you don't want them touched.
//   purifyCss: false, // Remove unused CSS selectors.
//   uglify: {}, // Uglify-specific options. https://webpack.github.io/docs/list-of-plugins.html#uglifyjsplugin
//   postCss: [] // Post-CSS options: https://github.com/postcss/postcss/blob/master/docs/plugins.md
// });
