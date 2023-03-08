const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const CopyPlugin = require( 'copy-webpack-plugin' );
// const replaceInFile = require( 'replace-in-file' );
const path = require( 'path' );
const { getArgFromCLI } = require( '@wordpress/scripts/utils' );
const outputPath = getArgFromCLI( '--output-path' );
const arveSrc = path.resolve(
	__dirname,
	'plugins/advanced-responsive-video-embedder/src'
);

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
					{
						from: 'plugins/advanced-responsive-video-embedder/php/Common/functions-assets.php',
						to: 'themes/symbiosis/php/Common2/functions-assets.php',
					},
				],
			} ),
		];
		break;
}

module.exports = config;
