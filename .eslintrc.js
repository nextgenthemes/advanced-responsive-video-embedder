module.exports = {
	root: true,
	parser: '@typescript-eslint/parser',
	parserOptions: {
		ecmaVersion: 2020,
		sourceType: 'module',
		// ecmaFeatures: {
		// 	modules: true,
		// },
	},
	plugins: ['@typescript-eslint', 'prettier'],
	extends: [
		// 'eslint:recommended',
		'plugin:@typescript-eslint/recommended',
		// 'plugin:prettier/recommended',
		'plugin:@wordpress/eslint-plugin/recommended',
	],
};

/*
npm i --save-dev \
    "@typescript-eslint/eslint-plugin@^3.2.0 \
    "@typescript-eslint/parser@^3.2.0 \
    "@wordpress/eslint-plugin@^6.1.0 \
    "browser-sync@^2.26.7 \
    "browser-sync-webpack-plugin@^2.2.2 \
    "copy-webpack-plugin@^6.0.2 \
    "eslint@^7.2.0 \
    "eslint-config-prettier@^6.11.0 \
    "eslint-plugin-prettier@^3.1.3 \
    "imagemin-mozjpeg@^8.0.0 \
    "imagemin-webpack-plugin@^2.4.2 \
    "json@latest \
    "laravel-mix@^5.0.4 \
    "laravel-mix-compress-images@^1.0.4 \
    "prettier@^2.0.5 \
    "resolve-url-loader@^3.1.1 \
    "sass@^1.26.8 \
    "sass-loader@^7.3.1 \
    "stylelint@^13.6.0 \
    "stylelint-config-twbs-bootstrap@^2.0.3 \
    "ts-loader@^7.0.5 \
    "typescript@^3.9.5 \
    "vue-template-compiler@^2.6.11"

*/
