const isProduction = process.env.NODE_ENV === 'production';
const postcssPlugins = require( '@wordpress/postcss-plugins-preset' );
const purgecssWithWordpress = require( 'purgecss-with-wordpress' );
const homedir = require( 'os' ).homedir();
const {
	getArgFromCLI,
	hasCssnanoConfig,
} = require( '@wordpress/scripts/utils' );
const outputPath = getArgFromCLI( '--output-path' );
const purgecss =
	'TODOthemes/symbiosis/build' === outputPath
		? [
				require( '@fullhuman/postcss-purgecss' )( {
					content: [
						'./themes/symbiosis/templates/**/*.html',
						'./themes/symbiosis/parts/**/*.html',
						'./themes/symbiosis/patterns/**/*.html',
						'./themes/symbiosis/php/**/*.php',
						'./themes/symbiosis/src/ts/**/*.ts',
						'./themes/symbiosis/src/ts/**/*.js',
						homedir + '/httrack/NGT/nextgenthemes.com/**/*.html',
					],
					safelist: [
						...purgecssWithWordpress.safelist,
						'alignwide',
						'alignfull',
					],
				} ),
		  ]
		: [];

module.exports = {
	ident: 'postcss',
	sourceMap: ! isProduction,
	plugins: isProduction
		? [
				...postcssPlugins,
				...purgecss,
				require( 'cssnano' )( {
					// Provide a fallback configuration if there's not
					// one explicitly available in the project.
					...( ! hasCssnanoConfig() && {
						preset: [
							'default',
							{
								discardComments: {
									removeAll: true,
								},
							},
						],
					} ),
				} ),
		  ]
		: postcssPlugins,
};
