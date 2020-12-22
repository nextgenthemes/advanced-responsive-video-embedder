/* eslint-disable no-undef */
module.exports = {
	env: {
		browser: true,
		node: false,
	},
	root: true,
	parser: '@typescript-eslint/parser',
	parserOptions: {
		ecmaVersion: 2020,
		//sourceType: 'module',
		// ecmaFeatures: {
		// 	modules: true,
		// },
	},
	plugins: ['@typescript-eslint', 'prettier'],
	extends: [
		'eslint:recommended',
		'plugin:@typescript-eslint/recommended',
		'plugin:prettier/recommended',
		'plugin:@wordpress/eslint-plugin/recommended',
	],
	rules: {
		'prettier/prettier': [
			'error',
			{
				useTabs: true,
				tabWidth: 4,
				printWidth: 90,
				singleQuote: true,
				trailingComma: 'es5',
				bracketSpacing: true,
				parenSpacing: true,
				jsxBracketSameLine: false,
				semi: true,
				arrowParens: 'always',
			},
		],
	},
	overrides: [
		{
			files: ['webpack.config.js'],
			env: {
				browser: false,
				node: true,
			},
		},
	],
};
