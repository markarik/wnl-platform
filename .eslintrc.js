module.exports = {
	extends: [
		'eslint:recommended',
		// TODO use recommended rules
		// 'plugin:vue/recommended'
		'plugin:vue/strongly-recommended'
	],
	plugins: [
		'import'
	],
	env: {
		browser: true,
		node: true,
		es6: true,
		amd: true
	},
	parserOptions: {
		ecmaVersion: 2018,
		sourceType: 'module'
	},
	rules: {
		'indent': [
			'error',
			'tab'
		],
		'linebreak-style': [
			'error',
			'unix'
		],
		'quotes': [
			'error',
			'single'
		],
		'semi': [
			'error',
			'always'
		],
		'no-unused-vars': [
			'error',
			{
				'ignoreRestSiblings': true
			}
		],
		'object-curly-spacing': [
			'error',
			'always'
		],

		// TODO enable eslint:recommended rules below
		'no-console': [
			'off'
		],
		'no-redeclare': [
			'off'
		],

		'import/no-relative-parent-imports': 'error',
		'vue/component-name-in-template-casing': [
			'error',
			'kebab-case'
		],
		// TODO enable strongly-recommended rules below
		'vue/html-indent': [
			'off'
		],
		'vue/require-prop-types': [
			'off'
		],
		'vue/singleline-html-element-content-newline': [
			'off'
		],
		'vue/mustache-interpolation-spacing': [
			'off'
		],
		'vue/max-attributes-per-line': [
			'off'
		],
		'vue/html-self-closing': [
			'off'
		],
		'vue/html-closing-bracket-newline': [
			'off'
		],
		'vue/html-closing-bracket-spacing': [
			'off'
		],
		'vue/multiline-html-element-content-newline': [
			'off'
		],
		'vue/require-default-prop': [
			'off'
		],
		'vue/no-template-shadow': [
			'off'
		],
	},
	globals: {
		$: true,
		$wnl: true,
		Echo: true,
		fbq: true,
		ga: true,
	}
};
