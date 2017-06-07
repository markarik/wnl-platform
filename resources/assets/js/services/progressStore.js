import store from 'store'
import axios from 'axios';
import {getApiUrl} from 'js/utils/env';
import {getCurrentUser} from './user';

const set = (key, value) => {
	store.set(key, value);


	getCurrentUser().then(({id}) => {
		axios.patch(getApiUrl(`users/${id}/state`), {
			value
		});
	});
};

const get = (key, userId) => {
	const localStorageValue = store.get(key);

	getCurrentUser().then(({id}) => {
		axios.get(getApiUrl(`users/${id}/state`));
	});

	return localStorageValue;
};

const remove = (key) => {
	store.remove(key);
	//TODO clear progress table
};


export default {
	get,
	set,
	remove
};
