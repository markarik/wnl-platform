import {set} from 'vue';
import * as types from 'js/store/mutations-types';

const namespaced = true;

export const sanitizeName = (name) => {
	return name === 'account deleted' ? 'Konto usunięte' : name;
};

export const state = {
	activeUsers: [],
	activeFilters: [],
};

export const getters = {
	activeUsers: state => channel =>  state[channel] || [],
	activeFilters: state => state.activeFilters,
};

export const mutations = {
	[types.ACTIVE_USERS_SET] (state, {users, channel}) {
		set(state, channel, users);
	},
};

export const actions = {
	userJoined ({commit, state}, {user, channel}) {
		const usersInChannel = state[channel] || [];

		commit(types.ACTIVE_USERS_SET, {users: [user, ...usersInChannel], channel});
	},
	userLeft({commit, state}, {user, channel}) {
		commit(types.ACTIVE_USERS_SET, {
			users: state[channel].filter((activeUser) => activeUser.id !== user.id),
			channel
		});
	},
	setActiveUsers({commit}, payload) {
		commit(types.ACTIVE_USERS_SET, payload);
	},
};

export default {
	state,
	getters,
	mutations,
	actions,
	namespaced
};
