import {isProduction} from './env'

export function gaEvent(category, action) {

	if (isProduction && typeof ga === 'function') {
		ga('send', 'event', category, action);
	}

}