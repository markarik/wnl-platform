import Vue from 'vue'
import Vuex from 'vuex'

// Global mutations, actions and getters
import mutations from 'js/store/mutations'
import * as actions from 'js/store/actions'
import * as getters from 'js/store/getters'

// Modules
import course from 'js/store/modules/course'
import collections from 'js/store/modules/collections'
import currentUser from 'js/store/modules/currentUser'
import navigation from 'js/store/modules/navigation'
import qna from 'js/store/modules/qna'
import quiz from 'js/store/modules/quiz'
import progress from 'js/store/modules/progress'
import slideshow from 'js/store/modules/slideshow'
import ui from 'js/store/modules/ui'
import activeUsers from 'js/store/modules/activeUsers'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
	state: {},
	getters,
	mutations,
	actions,
	modules: {
		course,
		collections,
		currentUser,
		navigation,
		qna,
		quiz,
		progress,
		slideshow,
		ui,
		activeUsers
	},
	strict: debug
})
