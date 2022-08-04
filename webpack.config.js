const fs = require( 'fs-extra' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const CopyPlugin = require( 'copy-webpack-plugin' );
// const replaceInFile = require( 'replace-in-file' );
const path = require( 'path' );
const { env } = require( 'process' );
const regex = /--output-path=([^\s'"]+)/s;
const matches = regex.exec( env.npm_lifecycle_script );
const arveSrc = path.resolve(
	__dirname,
	'plugins/advanced-responsive-video-embedder/src'
);
let outputPath = null;

if ( 1 in matches ) {
	outputPath = matches[ 1 ];
	console.log( '--output-path', matches[ 1 ] );
}

const config = defaultConfig;

switch ( outputPath ) {
	case 'plugins/advanced-responsive-video-embedder/build':
		config.entry = {
			...config.entry,
			main: arveSrc + '/main.ts',
			block: arveSrc + '/block.tsx',
			admin: arveSrc + '/admin.ts',
			'shortcode-ui': arveSrc + '/shortcode-ui.ts',
			settings: arveSrc + '/settings.ts',
		};
		config.resolve.alias = {
			...config.resolve.alias,
			vue: 'vue/dist/vue.js',
		};
		break;
	case 'themes/symbiosis/build':
		config.plugins = [
			...config.plugins,
			new CopyPlugin( {
				patterns: [
					{
						from: 'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
					},
				],
			} ),
		];
		break;
	case 'plugins/arve-pro/build':
		config.plugins = [
			...config.plugins,
			new CopyPlugin( {
				patterns: [
					{
						from: 'node_modules/lity/dist/lity.min.js',
					},
					{
						from: 'node_modules/lity/dist/lity.min.css',
					},
				],
			} ),
		];
		break;
}

module.exports = config;
