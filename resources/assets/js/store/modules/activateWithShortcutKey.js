import {set} from 'vue';
import * as types from 'js/store/mutations-types';

// Initial state
const state = {
	activeInstanceIndex: -1,
	isFocused: false,
	instances: [],
};

// Getters
const getters = {
	indexByUid: state => uid => state.instances.findIndex(instance => instance.uid === uid),
	isActiveByUid: (state, getters) => uid => state.activeInstanceIndex !== -1 && state.activeInstanceIndex === getters.indexByUid(uid),
	isFocusedByUid: (state, getters) => uid => state.isFocused && getters.isActiveByUid(uid),
};

// Mutations
export const mutations = {
	[types.ACTIVATE_WITH_SHORTCUT_KEY_REGISTER_INSTANCE] (state, uid) {
		state.instances.push({
			uid,
		});
	},
	[types.ACTIVATE_WITH_SHORTCUT_KEY_DEREGISTER_INSTANCE] (state, index) {
		if (state.activeInstanceIndex === index) {
			set(state, 'activeInstanceIndex', -1);
		}

		state.instances.splice(index, 1);

		if (state.activeInstanceIndex > index) {
			set(state, 'activeInstanceIndex', state.activeInstanceIndex - 1);
		}
	},
	[types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE_INDEX] (state, index) {
		set(state, 'activeInstanceIndex', index);
		set(state, 'isFocused', false);
	},
	[types.ACTIVATE_WITH_SHORTCUT_KEY_SET_FOCUSED] (state, isFocused = true) {
		set(state, 'isFocused', isFocused);
	},
};

// Actions
export const actions = {
	register({commit}, options) {
		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_REGISTER_INSTANCE, options);
	},
	deregister({commit, getters}, uid) {
		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_DEREGISTER_INSTANCE, getters.indexByUid(uid));
	},
	setActiveInstance({commit, getters}, uid) {
		const index = getters.indexByUid(uid);

		if (index > -1) {
			commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE_INDEX, index);
		}
	},
	setNextInstanceAsActive({commit, state}) {
		const length = state.instances.length;

		if (length === 0) {
			return;
		}

		const  index = state.activeInstanceIndex >= length - 1 ? 0 : state.activeInstanceIndex + 1;

		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE_INDEX, index);
	},
	setPreviousInstanceAsActive({commit, state}) {
		const length = state.instances.length;

		if (length === 0) {
			return;
		}

		const index = state.activeInstanceIndex <= 0 ? length - 1 : state.activeInstanceIndex - 1;

		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE_INDEX, index);
	},
	resetActiveInstance({commit}) {
		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE_INDEX, -1);
	},
	focusActiveInstance({commit, state}) {
		if (state.instances.length === 0) {
			return;
		}

		if (state.activeInstanceIndex === -1) {
			commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE_INDEX, 0);
		} else {
			commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_FOCUSED);
		}
	},
	resetFocus({commit}) {
		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_FOCUSED, false);
	}
};

export default {
	state,
	getters,
	mutations,
	actions,
	namespaced: true,
};
