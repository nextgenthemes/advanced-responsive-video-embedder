const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const ForkTsCheckerWebpackPlugin = require('fork-ts-checker-webpack-plugin');

module.exports = {
	...defaultConfig,
	entry: {
		main: './src/ts/main.ts',
		block: './src/ts/block.tsx',
		admin: './src/ts/admin.ts',
		'shortcode-ui': './src/ts/shortcode-ui.ts',
		'common/settings': './src/common/ts/settings.ts',
		'common/notice-ajax': './src/common/ts/notice-ajax.ts',
	},
	module: {
		...defaultConfig.module,
		rules: [
			{
				test: /\.tsx?$/,
				use: 'ts-loader',
				exclude: /node_modules/,
			},
			...defaultConfig.module.rules,
		],
	},
	resolve: {
		...defaultConfig.resolve,
		extensions: ['.tsx', '.ts', 'js', 'jsx'],
		alias: {
			vue: 'vue/dist/vue.js',
		},
	},
	output: {
		...defaultConfig.output,
		filename: '[name].js',
		path: __dirname + '/dist',
	},
};
