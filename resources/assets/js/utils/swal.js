import _ from 'lodash'

export function swalConfig(options = {}) {
	const defaults = {
		confirmButtonClass: 'button is-primary',
		cancelButtonClass: 'button is-outlined'
	}

	return _.merge(defaults, options)
}
