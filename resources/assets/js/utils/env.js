export function envValue(key) {
	return $wnl.env[key]
}

// Debug

export function isDebug() {
	return envValue('appDebug')
}

// URLs

export function getUrl(path) {
	return `${envValue('appUrl')}/${path}`
}

export function getApiUrl(path) {
	return getUrl(`papi/v1/${path}`)
}

export function getImageUrl(filename) {
	return getUrl(`images/${filename}`)
}
