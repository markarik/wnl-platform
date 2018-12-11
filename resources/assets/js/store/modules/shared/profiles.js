import * as types from '../../mutations-types';
import {set} from 'vue';

const profiles = {
	state () {
		return {};
	},

	getters: {
		getProfileById: state => id => state[id],
	},

	actions: {
		setProfiles({commit}, profiles) {
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
