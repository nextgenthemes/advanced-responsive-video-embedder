const fs = require('fs-extra');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const CopyPlugin = require('copy-webpack-plugin');
const replaceInFile = require('replace-in-file');
const path = require('path');
const { exit, cwd, env } = require('process');
//const { getArgFromCLI } = require('@wordpress/scripts/utils');

//const project = getArgFromCLI('--project');
const project = process.env.npm_lifecycle_event.split(/:/)[1];
console.log('Project:', project);

if (undefined === project) {
	exit(1);
}

const srcDir = './plugins/' + project + '/src';
// all work by why use them?
//const srcDir = path.resolve(cwd(), 'plugins', project, 'src');
//const srcDir = path.resolve(__dirname, 'plugins', project, 'src');

const config = {
	...defaultConfig,
	entry: {
		main: srcDir + '/main.ts',
	},
	output: {
		...defaultConfig.output,
		path: path.resolve(__dirname, 'plugins', project, 'build'),
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
		symlinks: false,
		alias: {
			vue: 'vue/dist/vue.js',
		},
	},
};

switch (project) {
	case 'advanced-responsive-video-embedder':
		config.entry = {
			...config.entry,
			block: srcDir + '/block.tsx',
			admin: srcDir + '/admin.ts',
			'shortcode-ui': srcDir + '/shortcode-ui.ts',
			settings: srcDir + '/settings.ts',
		};
		break;

	case 'symbiosis':
		prepareSCSS();
		config.entry = {
			main: srcDir + '/ts/main.ts',
			essential: srcDir + '/ts/essential.ts',
			customizepreview: srcDir + '/ts/customizepreview.ts',
			settings: './plugins/advanced-responsive-video-embedder/src/settings.ts',
		};
		config.plugins = [
			...config.plugins,
			new CopyPlugin({
				patterns: [
					{
						from: 'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
					},
				],
			}),
		];
		break;

	case 'arve-pro':
		config.plugins = [
			...config.plugins,
			new CopyPlugin({
				patterns: [
					{
						from: 'node_modules/lity/dist/lity.min.js',
					},
					{
						from: 'node_modules/lity/dist/lity.min.css',
					},
				],
			}),
		];
		break;
}

function prepareSCSS() {
	try {
		fs.copySync(
			'node_modules/bootstrap/scss',
			'plugins/symbiosis/src/scss/bootstrap'
		);
		const results = replaceInFile.sync({
			files: 'plugins/symbiosis/src/scss/bootstrap.scss',
			from: [
				'@import "root";',
				'@import "reboot";',
				'@import "type";',
				'@import "images";',
			],
			to: [
				'//removed root',
				'//removed reboot',
				'//removed type',
				'//removed images',
			],
		});
		console.log('Replacement results:', results);
	} catch (error) {
		console.error('Error occurred:', error);
	}
}

module.exports = config;
