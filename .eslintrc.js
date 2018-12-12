module.exports = {
	"env": {
		"browser": true,
		"node": true,
		"es6": true,
		"amd": true
	},
	"parserOptions": {
		"ecmaVersion": 2018,
		"sourceType": "module"
	},
	"rules": {
		"indent": [
			"error",
			"tab"
		],
		"linebreak-style": [
			"error",
			"unix"
		],
		"quotes": [
			"error",
			"single"
		],
		"semi": [
			"error",
			"always"
		]
	},
	"globals": {
		"$": true,
		"$wnl": true
	}
};
