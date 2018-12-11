import {set} from 'vue';
import * as types from '../mutations-types';
import uuidv1 from 'uuid/v1';


// Initial state
const state = {
	alerts: []
};

// Getters
const getters = {
	alerts: state => state.alerts,
};

// Mutations
export const mutations = {
	[types.GLOBAL_ALERTS_ADD_ALERT] (state, alert) {
		set(state, 'alerts', [...state.alerts, alert]);
	},
	[types.GLOBAL_ALERTS_CLOSE_ALERT] (state, {id}) {
		const filteredList = state.alerts.filter(alert => alert.id !== id);
		set(state, 'alerts', filteredList);
	},
};

// Actions
export const actions = {
	addAlert({commit}, paylaod) {
		const id = uuidv1();
		commit(types.GLOBAL_ALERTS_ADD_ALERT, {id, ...paylaod});
		return id;
	},
	closeAlert({commit, state}, payload) {
		commit(types.GLOBAL_ALERTS_CLOSE_ALERT, payload);
	},
	addAutoDismissableAlert({commit, dispatch}, {timeout, ...payload}) {
		const timeoutWithDefault = timeout || 5000;
		dispatch('addAlert', payload)
			.then(id => {
				setTimeout(() => {
					dispatch('closeAlert', {id});
				}, timeoutWithDefault);
			});
	},
};

export default {
	state,
	getters,
	mutations,
	actions
};
