import {get} from 'lodash';

const PLACEHOLDER_RGX = /{{[a-z0-9]*}}/gi;

export default function injectArguments(content, args) {
	const matches = content.match(PLACEHOLDER_RGX);
	let missing = [];

	if (!matches) return content;

	matches.forEach(match => {
		const argName = match.replace(/{{|}}/g, '');

		const value = get(args, [argName, 'value']);
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
