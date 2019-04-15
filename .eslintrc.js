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
		'vue/require-prop-types': [
			'off'
		],
		// Doesn't seem useful
		'vue/singleline-html-element-content-newline': [
			'off'
		],
		'vue/mustache-interpolation-spacing': [
			'error',
			'never',
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
