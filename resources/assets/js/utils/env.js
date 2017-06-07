export function envValue(key) {
	return $wnl.env[key]
}

// Env

export function isDev() {
	return envValue('appEnv') === 'dev'
}

export function isDemo() {
	return envValue('appEnv') === 'demo'
}

export function isProduction() {
	return envValue('appEnv') === 'production'
}

// Debug control

export function isDebug() {
	return envValue('appDebug')
}

export function useLocalStorage() {
	return envValue('APP_USE_LOCAL_STORAGE')
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
