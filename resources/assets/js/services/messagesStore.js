import engine from 'store/src/store-engine'
import cookieStorage from 'store/storages/cookieStorage'

const store = engine.createStore([
	cookieStorage,
]);

export default store
