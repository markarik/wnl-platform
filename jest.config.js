module.exports = {
	moduleNameMapper: {
		'^js/(.*)$': '<rootDir>/resources/assets/js/$1',
		'^images/(.*)$': '<rootDir>/resources/assets/images/$1',
	},
	transform: {
		'^.+\\.js$': 'babel-jest',
		'.*\\.(vue)$': 'vue-jest',
		'^.+\\.svg$': '<rootDir>/svgTransformer.js'
	},
	moduleFileExtensions: ['js', 'vue', 'json'],
	setupFiles: ['./resources/assets/js/tests/setup.js']
};
