// const { log } = console;
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const WebpackNotifierPlugin = require( 'webpack-notifier' );

let config = defaultConfig;

// when --experimental-modules is NOT passed, the config is a single object, when its passed its an array with two config objects. For consistency, always make it any array
if ( ! Array.isArray( config ) ) {
	config = [ config ];
}

config = config.map( ( conf ) => {
	return {
		...conf,
		plugins: [ ...conf.plugins, new WebpackNotifierPlugin( { onlyOnError: true } ) ],
	};
} );

module.exports = config;
