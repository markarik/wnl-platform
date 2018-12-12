import moment from 'moment';
import 'moment/locale/pl';

let today = moment(new Date());

export function justTimeFromS(timestamp) {
	return moment(timestamp * 1000).format('H:mm');
}

export function justMonthAndDayFromS(timestamp) {
	return moment(timestamp * 1000).format('D MMM');
}

export function timeFromS(timestamp) {
	return timeFromMs(timestamp * 1000);
}

export function timeFromMs(timestamp) {
	return moment(timestamp).format('LLL');
}

export function shortTimeFromMs(timestamp) {
	let time = moment(timestamp),
		diff = today.diff(time, 'days');

	if (diff === 0) { // today
		return time.format('LT');
	}

	if (diff > 0 && diff < 7) { // not today && not older than a week
		return time.format('dd');
	}

	// more than a week
	return time.format('DD/MM/YY');
}

export function timeFromDate(date) {
	return moment(date).format('LL');
}

export function msFromSeconds(s) {
	return moment('2015-01-01').startOf('day').seconds(s).format('mm:ss');
}

export function hmsFromSeconds(s) {
	return moment('2015-01-01').startOf('day').seconds(s).format('H:mm:ss');
}
