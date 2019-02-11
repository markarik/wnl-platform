import {set} from 'vue';
import * as types from 'js/store/mutations-types';

// Initial state
const state = {
	activeInstanceIndex: -1,
	instances: [],
};

// Getters
const getters = {
	indexByUid: state => uid => state.instances.findIndex(instance => instance.uid === uid)
};

// Mutations
export const mutations = {
	[types.ACTIVATE_WITH_SHORTCUT_KEY_REGISTER_INSTANCE] (state, {
		uid,
		onActivate = () => {},
		onDeactivate = () => {},
		onFocus = () => {},
	}) {
		state.instances.push({
			uid,
			onActivate,
			onDeactivate,
			onFocus,
		});
	},
	[types.ACTIVATE_WITH_SHORTCUT_KEY_DEREGISTER_INSTANCE] (state, index) {
		if (state.activeInstanceIndex === index) {
			set(state, 'activeInstanceIndex', -1);
		}

		state.instances.splice(index, 1);
		// TODO update active index if needed
	},
	[types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE_INDEX] (state, index) {
		set(state, 'activeInstanceIndex', index);
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
			if (state.activeInstanceIndex > -1) {
				state.instances[state.activeInstanceIndex].onDeactivate();
			}

			commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE_INDEX, index);
			state.instances[index].onActivate();
		}
	},
	setFirstInstanceAsActive({commit, state}) {
		if (state.instances.length > 0) {
			commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE_INDEX, 0);
			state.instances[0].onActivate();
		}
	},
	setNextInstanceAsActive({commit, state}) {
		const length = state.instances.length;

		if (length === 0) {
			return;
		}

		let index = state.activeInstanceIndex + 1;

		if (index >= length) {
			index = 0;
		}

		if (state.activeInstanceIndex > -1) {
			state.instances[state.activeInstanceIndex].onDeactivate();
		}

		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE_INDEX, index);
		state.instances[index].onActivate();
	},
	setPreviousInstanceAsActive({commit, state}) {
		const length = state.instances.length;

		if (length === 0) {
			return;
		}

		let index = state.activeInstanceIndex - 1;

		if (index < 0) {
			index = length - 1;
		}

		if (state.activeInstanceIndex > -1) {
			state.instances[state.activeInstanceIndex].onDeactivate();
		}

		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE_INDEX, index);
		state.instances[index].onActivate();
	},
	resetActiveInstance({commit}) {
		if (state.activeInstanceIndex > -1) {
			state.instances[state.activeInstanceIndex].onDeactivate();
		}

		commit(types.ACTIVATE_WITH_SHORTCUT_KEY_SET_ACTIVE_INSTANCE_INDEX, -1);
	},
	focusActiveInstance({dispatch, state}) {
		if (state.activeInstanceIndex === -1) {
			dispatch('setFirstInstanceAsActive');
		} else {
			state.instances[state.activeInstanceIndex].onFocus();
		}

	}
};

export default {
	state,
	getters,
	mutations,
	actions,
	namespaced: true,
};
