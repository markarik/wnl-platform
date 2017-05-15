import _ from 'lodash'
import store from 'store'
import { set } from 'vue'
import { getApiUrl } from 'js/utils/env'
import { resource } from 'js/utils/config'
import * as types from 'js/admin/store/mutations-types'

// Helper functions
function getCourseApiUrl(courseId) {
	return getApiUrl(
		`${resource('editions')}/${courseId}?include=${resource('groups')}.${resource('lessons')}.${resource('screens')}.${resource('sections')}`
	)
}

// Namespace
const namespaced = true

// Initial state
const state = {
	ready: false,
	lessons: {},
}

// Getters
const getters = {
	allLessons: state => state.lessons,
}

// Mutations
const mutations = {
	// [types.COURSE_READY] (state) {
	// 	set(state, 'ready', true)
	// },
	// [types.SET_STRUCTURE] (state, data) {
	// 	set(state, 'id', data.id)
	// 	set(state, 'name', data.name)
	// 	set(state, resource('groups'), data[resource('groups')])
	// 	set(state, 'structure', data.included)
	// }
}

// Actions
const actions = {
	setup({ commit, dispatch }, courseId) {
		Promise.all([
			dispatch('setStructure', courseId),
			dispatch('progress/setupCourse', courseId, {root: true}),
		]).then(resolutions => {
			$wnl.logger.debug('Course ready, yay!')
			commit(types.COURSE_READY)
		}, reason => {
			$wnl.logger.error(reason)
		})
	},
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
