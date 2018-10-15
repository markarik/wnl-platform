import Vue from 'vue'
import Vuex from 'vuex'

// Global mutations, actions and getters
import mutations from 'js/store/mutations'
import * as actions from 'js/store/actions'

// Modules
import chat from 'js/store/modules/chat'
import chatMessages from 'js/store/modules/chatMessages'
import course from 'js/store/modules/course'
import collections from 'js/store/modules/collections'
import notifications from 'js/store/modules/notifications'
import currentUser from 'js/store/modules/currentUser'
import navigation from 'js/store/modules/navigation'
import qna from 'js/store/modules/qna'
import comments from 'js/store/modules/comments'
import quiz from 'js/store/modules/quiz'
import questions from 'js/store/modules/questions'
import progress from 'js/store/modules/progress'
import slideshow from 'js/store/modules/slideshow'
import ui from 'js/store/modules/ui'
import users from 'js/store/modules/users'
import alerts from 'js/store/modules/alerts'
import autocomplete from 'js/store/modules/autocomplete'
import tasks from 'js/store/modules/tasks'
import flashcards from 'js/store/modules/flashcards'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
	state: {},
	mutations,
	actions,
	modules: {
		chat,
		chatMessages,
		course,
		collections,
		notifications,
		currentUser,
		navigation,
		qna,
		quiz,
		questions,
		progress,
		slideshow,
		ui,
		users,
		autocomplete,
		comments,
		tasks,
		alerts,
		flashcards
	},
	strict: debug
})
