module.exports = {
	extends: [
		'eslint:recommended',
		'plugin:vue/recommended',
	],
	plugins: [
		'import'
	],
	env: {
		browser: true,
		node: true,
		es6: true,
		amd: true,
		jest: true
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
		'import/no-relative-parent-imports': 'error',
		'vue/component-name-in-template-casing': [
			'error',
			'kebab-case'
		],
		'vue/html-indent': [
			'error',
			'tab',
			{
				'ignores': [
					'VElement[name=thead].children',
					'VElement[name=tbody].children',
				]
			}
		],
		'vue/mustache-interpolation-spacing': [
			'error',
			'never',
		],
		'vue/max-attributes-per-line': [
			'error',
			{
				'singleline': 2,
			}
		],
		'vue/attributes-order': [
			'error',
		],

		// TODO enable vue/strongly-recommended rules below
		'vue/require-prop-types': [
			'off'
		],
		'vue/require-default-prop': [
			'off'
		],

		// We use v-html a lot
		'vue/no-v-html': [
			'off'
		],

		// The two below don't seem useful
		'vue/singleline-html-element-content-newline': [
			'off'
		],
		'vue/multiline-html-element-content-newline': [
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
