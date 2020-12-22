const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
	...defaultConfig,
	entry: {
		main: './src/main.ts',
		block: './src/block.tsx',
		admin: './src/admin.ts',
		'shortcode-ui': './src/shortcode-ui.ts',
		'common/settings': './src/common/settings.ts',
		'common/notice-ajax': './src/common/notice-ajax.ts',
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
};
