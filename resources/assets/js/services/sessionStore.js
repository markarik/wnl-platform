import engine from 'store/src/store-engine';
import sessionStorage from 'store/storages/sessionStorage';
import localStorage from 'store/storages/localStorage';

const store = engine.createStore([
	sessionStorage,
	localStorage
]);

export default store;

