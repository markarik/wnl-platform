import {set} from 'vue';
import * as types from 'js/store/mutations-types';

// Initial state
const state = {
	activeInstanceIndex: -1,
	instances: [],
};

// Getters
const getters = {
	activeInstance: state => state.instances[state.activeInstanceIndex] || null,
};

// Mutations
export const mutations = {
	[types.ACTIVATE_WITH_SHORTCUT_KEY_REGISTER_INSTANCE] (state, uid) {
		state.instances.push(uid);
	},
	[types.ACTIVATE_WITH_SHORTCUT_KEY_UNREGISTER_INSTANCE] (state, uid) {
		const index = state.instances.indexOf(uid);

		state.instances.splice(index, 1);

		if (state.activeInstanceIndex === index) {
			set(state, 'activeInstanceIndex', -1);
		}
	},
	[types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE] (state, index) {
		set(state, 'activeInstanceIndex', index);
	},
};

// Actions
export const actions = {
	register({commit}, uid) {
		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_REGISTER_INSTANCE, uid);
	},
	unregister({commit}, uid) {
		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_UNREGISTER_INSTANCE, uid);
	},
	setActiveInstance({commit}, uid) {
		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE, state.instances.indexOf(uid));
	},
	setFirstInstanceAsActive({commit}) {
		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE, 0);
	},
	setNextInstanceAsActive({commit, state}) {
		const length = state.instances.length;
		let index = state.activeInstanceIndex + 1;

		if (index >= length) {
			index = 0;
		}

		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE, index);
	},
	setPreviousInstanceAsActive({commit, state}) {
		const length = state.instances.length;
		let index = state.activeInstanceIndex - 1;

		if (index < 0) {
			index = length - 1;
		}

		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE, index);
	},
	resetActiveInstance({commit}) {
		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE, -1);
	},
};

export default {
	state,
	getters,
	mutations,
	actions,
	namespaced: true,
};
