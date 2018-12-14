function getInitial(text) {
	return text.charAt(0).toUpperCase();
}

export function getInitials(username) {
	let hasSpaceRegex = /\s/;
	if (username.length === 1) {
		return username.toUpperCase();
	} else if (hasSpaceRegex.test(username)) {
		let split = username.split(' ');
		return getInitial(split[0]) + getInitial(split[1]);
	} else {
		return username.slice(0, 2).toUpperCase();
	}
}
