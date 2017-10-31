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
    activeUsers: state => state.activeUsers,
    activeFilters: state => state.activeFilters,
    // getUsersByLoaction: function(state) {
    //     return function(city) {
    //         return state.allUsers.filter((item) => {
    //             return item.city === city
    //         })
    //     }
    // },
    // getUsersByLoaction: state => city => state.allUsers.filter(item => item.city.toLowerCase().indexOf(city.toLowerCase())),
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
    // getUsersByLoaction: function(state) {
    //     return function(city) {
    //         return state.allUsers.filter((item) => {
    //             debugger
    //             return item.city.toLowerCase().indexOf(city.toLowerCase())
    //         })
    //     }
    // },
    // return this.availableModerators.filter(moderator => moderator.full_name.toLowerCase().indexOf(this.assigneeText.toLowerCase()) > -1).slice(0, 5)
    getUsersByRole: state => role => state.allUsers.filter(item => item.roles.includes(role)),
};

const mutations = {
    [types.ACTIVE_USERS_SET] (state, activeUsers) {
		set(state, 'activeUsers', activeUsers)
	},
    [types.ALL_USERS_SET] (state, response) {
        set(state, 'allUsers', response.data)
    },
};

// dupa({commit})  -> axiosem pobrac userow wszystkich i zacommitowac razem z mutacja
const actions = {
    userJoined ({commit, state}, user) {
		commit(types.ACTIVE_USERS_SET, [user, ...state.activeUsers])
	},
	userLeft({commit, state}, user) {
		commit(types.ACTIVE_USERS_SET, state.activeUsers.filter((activeUser) => activeUser.id !== user.id))
	},
	setActiveUsers({commit}, users) {
		commit(types.ACTIVE_USERS_SET, users)
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
