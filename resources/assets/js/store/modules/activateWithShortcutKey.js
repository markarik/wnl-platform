import {set} from 'vue';
import * as types from 'js/store/mutations-types';

// Initial state
const state = {
	activeInstanceUid: null,
	instances: [],
};

// Getters
const getters = {
	activeInstance: state => state.activeInstanceUid,
};

// Mutations
export const mutations = {
	[types.ACTIVATE_WITH_SHORTCUT_KEY_REGISTER_INSTANCE] (state, uid) {
		state.instances.push(uid);
	},
	[types.ACTIVATE_WITH_SHORTCUT_KEY_UNREGISTER_INSTANCE] (state, uid) {
		const index = state.instances.indexOf(uid);

		if (state.activeInstanceUid === uid) {
			set(state, 'activeInstanceUid', null);
		}

		state.instances.splice(index, 1);
	},
	[types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE] (state, uid) {
		set(state, 'activeInstanceUid', uid);
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
		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE, uid);
	},
	setFirstInstanceAsActive({commit}) {
		if (state.instances.length > 0) {
			commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE, state.instances[0]);
		}
	},
	setNextInstanceAsActive({commit, state}) {
		const length = state.instances.length;

		if (length === 0) {
			return;
		}

		let index = state.instances.indexOf(state.activeInstanceUid) + 1;

		if (index >= length) {
			index = 0;
		}

		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE, state.instances[index]);
	},
	setPreviousInstanceAsActive({commit, state}) {
		const length = state.instances.length;

		if (length === 0) {
			return;
		}

		let index = state.instances.indexOf(state.activeInstanceUid) - 1;

		if (index < 0) {
			index = length - 1;
		}

		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE, state.instances[index]);
	},
	resetActiveInstance({commit}) {
		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE, null);
	},
};

export default {
	state,
	getters,
	mutations,
	actions,
	namespaced: true,
};
