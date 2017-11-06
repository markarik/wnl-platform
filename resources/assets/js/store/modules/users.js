import {set} from 'vue'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'

const namespaced = true;

const state = {
    allUsers: [],
    activeUsers: [],
    activeFilters: [],
    usersByLocation: [],
    usersByRole: [],
};

const getters = {
    allUsers: state => state.allUsers,
    activeUsers: state => channel =>  state[channel] || [],
    activeFilters: state => state.activeFilters,
    getUsersByLoaction: function(state) {
        return function(city) {
            return state.allUsers.filter(function(item) {
                if (item.city === null) {
                    return item.city === ''
                }
                return item.city.toLowerCase().indexOf(city.toLowerCase()) === 0
            })
        }
    },
    getUsersByHelp: function(state) {
        return function(city) {
            return state.allUsers.filter(function(item) {
                if (item.help === null) {
                    return item.help === ''
                }
                return item.help.toLowerCase().indexOf(help.toLowerCase()) === 0
            })
        }
    },
    getUsersByRole: state => role => state.allUsers.filter(item => item.roles.includes(role)),
};

const mutations = {
    [types.ACTIVE_USERS_SET] (state, {users, channel}) {
		set(state, channel, users)
	},
    [types.ALL_USERS_SET] (state, response) {
        set(state, 'allUsers', response.data)
    },
};

const actions = {
    userJoined ({commit, state}, {user, channel}) {
        const usersInChannel = state[channel] || [];

        commit(types.ACTIVE_USERS_SET, {users: [user, ...usersInChannel], channel})
    },
    userLeft({commit, state}, {user, channel}) {
        commit(types.ACTIVE_USERS_SET, {
            users: state[channel].filter((activeUser) => activeUser.id !== user.id),
            channel
        })
    },
    setActiveUsers({commit}, payload) {
        commit(types.ACTIVE_USERS_SET, payload)
    },
    setAllUsers({commit}) {
        axios.get(getApiUrl('users/all/profile')).then(function(response) {
            commit(types.ALL_USERS_SET, response)
        })
    },
};

export default {
	state,
	getters,
	mutations,
	actions,
    namespaced
}
