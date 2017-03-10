export function configValue(key) {
	return $wnl.config[key]
}

export function resource(key) {
	return configValue('papi').resources[key]
}
