import store from 'store' // localStorage wrapper/polyfill

const set = (key, value) => {
	store.set(key, value);

	//TODO save progress to DB
};

const get = (key) => {
	return store.get(key);

	//TODO retrieve progress from DB
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
