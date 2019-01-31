import _ from 'lodash';

export function swalConfig(options = {}) {
	const defaults = {
		buttonsStyling: false,
		confirmButtonColor: '#3f9fa7',
		confirmButtonClass: 'button is-primary',
		cancelButtonClass: 'button is-outlined',
		customClass: 'wnl-alert',
	};

	return _.merge(defaults, options);
}
