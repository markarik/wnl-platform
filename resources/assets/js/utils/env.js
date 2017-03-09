export function envValue(key) {
	return $wnl.env[key]
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
