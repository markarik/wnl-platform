module.exports = {
	extends: [
		// TODO use recommended rules
		// 'plugin:vue/recommended'
		'plugin:vue/strongly-recommended'
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
		// TODO enable essential rules below
		'vue/no-use-v-if-with-v-for': [
			'off'
		],
		// TODO enable strongly-recommended rules below
		'vue/html-indent': [
			'off'
		],
		'vue/require-prop-types': [
			'off'
		],
		'vue/component-name-in-template-casing': [
			'off'
		],
		'vue/singleline-html-element-content-newline': [
			'off'
		],
		'vue/mustache-interpolation-spacing': [
			'off'
		],
		'vue/attribute-hyphenation': [
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
		'vue/name-property-casing': [
			'off'
		],
	},
	globals: {
		$: true,
		$wnl: true
	}
};
