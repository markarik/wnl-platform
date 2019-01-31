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
		// TODO enable this rule
		'vue/no-use-v-if-with-v-for': [
			'off'
		],
	},
	globals: {
		$: true,
		$wnl: true
	}
};
