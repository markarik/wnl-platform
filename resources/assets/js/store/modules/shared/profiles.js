import * as types from 'js/store/mutations-types';

const profiles = {
	state () {
		return {};
	},

	getters: {
		getProfileById: state => id => state[id],
	},

	actions: {
		setProfiles({ commit }, profiles) {
			commit(types.PROFILES_SET, profiles);
		}

	},

	mutations: {
		[types.PROFILES_SET](state, payload) {
			Object.assign(state, payload);
		},
	}
};


export default profiles;
