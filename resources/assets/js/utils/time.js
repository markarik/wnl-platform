import moment from 'moment'
import 'moment/locale/pl'

export function timeFromS(timestamp) {
	return timeFromMs(timestamp * 1000)
}

export function timeFromMs(timestamp) {
	return moment(timestamp).format('LLL')
}
