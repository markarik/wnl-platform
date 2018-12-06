const PLACEHOLDER_RGX = /{{(.*)}}/g;

export default function injectArguments(content, args) {
	const matches = content.match(PLACEHOLDER_RGX);
	let missing = [];

	if (!matches) return content;

	matches.forEach(match => {
		const argName = match.replace(/{{|}}/g, '');
		const value = args[argName] || '';
		if (!value) missing.push(argName);
		content = content.replace(match, value);
	});

	if (missing.length > 0) {
		$wnl.logger.warning(
			'Missing template arguments: ' + missing.join(',')
		);
	}

	return content;
}
