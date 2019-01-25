module.exports = {
	extends: [
		// TODO use recommended rules
		// 'plugin:vue/recommended'
		'plugin:vue/essential'
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
		// TODO enable these rules, one by one
		'vue/require-prop-type-constructor': [
			'off'
		],
		'vue/require-valid-default-prop': [
			'off'
		],
		'vue/no-use-v-if-with-v-for': [
			'off'
		],
		'vue/valid-v-for': [
			'off'
		],
		'vue/require-v-for-key': [
			'off'
		],
	},
	globals: {
		$: true,
		$wnl: true
	}
};
