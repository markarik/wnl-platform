import engine from 'store/src/store-engine'
import localStorage from 'store/storages/localStorage'

const store = engine.createStore([
	localStorage,
]);

export default store
