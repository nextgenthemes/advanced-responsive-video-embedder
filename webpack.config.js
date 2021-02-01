const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const { exit, cwd, env } = require('process');
const project = env.NPROJECT;

if ( undefined === project ) {
	exit(1);
}

const config = {
	...defaultConfig,
	entry: {
		main: path.resolve( cwd(), project, 'src', 'main.ts' ),
	},
	output: {
		...defaultConfig.output,
		path: path.resolve(cwd(), project, 'build'),
	},
	module: {
		...defaultConfig.module,
		rules: [
			...defaultConfig.module.rules,
			{
				test: /\.tsx?$/,
				use: 'ts-loader',
				exclude: /node_modules/,
			},
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

if ('advanced-responsive-video-embedder' === project ) {
	config.entry = {
		main: path.resolve(cwd(), project, 'src', 'main.ts'),
		block: path.resolve(cwd(), project, 'src', 'block.tsx'),
		admin: path.resolve(cwd(), project, 'src', 'admin.ts'),
		'shortcode-ui': path.resolve(cwd(), project, 'src', 'shortcode-ui.ts'),
		'common/settings': path.resolve(cwd(), project, 'src', 'common', 'settings.ts'),
	};
}

module.exports = config;
