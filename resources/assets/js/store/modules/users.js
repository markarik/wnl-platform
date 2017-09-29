import {set} from 'vue'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'

const state = {
    activeUsers: [],
    allUsers: [],
};

const getters = {
    allusers: state => state.allUsers,
    activeUsers: state => state.activeUsers,
    // getUsersByLoaction: function(state) {
    //     return function(city) {
    //         return state.allUsers.filter((item) => {
    //             return item.city === city
    //         })
    //     }
    // },
    getUsersByLoaction: state => city => state.allUsers.filter(item => item.city === city)
};

const mutations = {
    [types.ACTIVE_USERS_SET] (state, activeUsers) {
		set(state, 'activeUsers', activeUsers)
	},
    [types.ALL_USERS_SET] (state, payload) {
        set(state, 'allUsers', payload)
    }
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
    }
};

export default {
	state,
	getters,
	mutations,
	actions
}
