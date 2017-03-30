import Vue from 'vue'
import Vuex from 'vuex'

// Global mutations, actions and getters
import mutations from 'js/store/mutations'
import * as actions from 'js/store/actions'
import * as getters from 'js/store/getters'

// Modules
import users from 'js/store/modules/users'
import progress from 'js/store/modules/progress'
import course from 'js/store/modules/course'
import qna from 'js/store/modules/qna'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
	state: {},
	getters,
	mutations,
	actions,
	modules: {
		users,
		progress,
		course,
		qna,
	},
	strict: debug
})
