export const fontColors = [false, '#BD2318', '#CC3E17', '#ECA005', '#389476', '#2F4999', '#62378d']

export const getColourForStr = (str) => {
	let hash = 0;
	for (let i = 0; i < str.length; i++) {
		hash = str.charCodeAt(i) + ((hash << 5) - hash);
	}

	let hex =
		((hash >> 24) & 0xff).toString(16) +
		((hash >> 16) & 0xff).toString(16) +
		((hash >> 8) & 0xff).toString(16) +
		(hash & 0xff).toString(16);

	hex += "000000";
	hex = hex.substring(0, 6)
	const r = parseInt(hex.substring(0, 2), 16);
	const g = parseInt(hex.substring(2, 4), 16);
	const b = parseInt(hex.substring(4, 6), 16);

	return `rgba(${r}, ${g}, ${b}, 0.2)`
}
