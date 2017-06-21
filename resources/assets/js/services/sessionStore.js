import engine from 'store/src/store-engine';
import sessionStorage from 'store/storages/sessionStorage';
import localStorage from 'store/storages/localStorage';
import cookieStorage from 'store/storages/cookieStorage';

const store = engine.createStore([
	sessionStorage,
	localStorage,
	cookieStorage
]);

export default store;

