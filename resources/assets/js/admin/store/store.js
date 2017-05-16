import Vue from 'vue'
import Vuex from 'vuex'

// Global mutations, actions and getters
import mutations from 'js/admin/store/mutations'
import * as actions from 'js/admin/store/actions'
import * as getters from 'js/admin/store/getters'

// Modules
import currentUser from 'js/store/modules/currentUser'
import lessons from 'js/admin/store/modules/lessons'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
	state: {},
	getters,
	mutations,
	actions,
	modules: {
		currentUser,
		lessons,
	},
	strict: debug
})
