import {set} from 'vue'
import * as types from '../mutations-types'
import uuidv1 from 'uuid/v1';


// Initial state
const state = {
    alerts: []
};

// Getters
const getters = {
	alerts: state => state.alerts,
}

// Mutations
export const mutations = {
	[types.GLOBAL_ALERTS_ADD_ALERT] (state, alert) {
		set(state, alerts, [...state.alerts, alert])
    },
    [types.GLOBAL_ALERTS_CLOSE_ALERT] (state, {id}) {
		const filteredList = alerts.filter(alert => alert.id !== id);
		set(state, alerts, filteredList)
	},
}

// Actions
export const actions = {
	addAlert({commit}, {text, type}) {
		commit(types.GLOBAL_ALERTS_ADD_ALERT, {text, type, id: uuidv1()})
	},
	closeAlert({commit, state}, {user, channel}) {
		commit(types.ACTIVE_USERS_SET, {
			users: state[channel].filter((activeUser) => activeUser.id !== user.id),
			channel
		})
	}
};

export default {
	state,
	getters,
	mutations,
	actions
}
