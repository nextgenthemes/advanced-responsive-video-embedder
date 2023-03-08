/* eslint-disable no-undef */
module.exports = {
	env: {
		browser: true,
		node: false,
	},
	extends: [
		// 'eslint:recommended',
		// 'plugin:@typescript-eslint/recommended',
		// 'plugin:prettier/recommended',
		'plugin:@wordpress/eslint-plugin/recommended',
	],
	overrides: [
		{
			files: [ 'webpack.config*.js', 'postcss.config.js' ],
			env: {
				browser: false,
				node: true,
			},
			rules: {
				'no-console': 'off',
				'@typescript-eslint/no-var-requires': 'off',
			},
		},
	],
};
