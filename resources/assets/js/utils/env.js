export function envValue(key) {
	return $wnl.env[key];
}

// Env

export function isDev() {
	return envValue('appEnv') === 'dev';
}

export function isDemo() {
	return envValue('appEnv') === 'demo';
}

export function isProduction() {
	return envValue('appEnv') === 'production';
}

// Debug control

export function isDebug() {
	return envValue('appDebug');
}

// URLs

export function getUrl(path) {
	const delimiter = path.indexOf('/') === 0 ? '' : '/';
	return `${envValue('appUrl')}${delimiter}${path}`;
}

export function getApiUrl(path) {
	return getUrl(`papi/v2/${path}`);
}

export function getImageUrl(filename) {
	return getUrl(`images/${filename}`);
}
