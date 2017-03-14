import store from 'store'
import { set } from 'vue'
import { getApiUrl } from 'js/utils/env'
import { resource } from 'js/utils/config'
import * as types from 'js/store/mutations-types'

// Helper functions
function getCourseApiUrl(courseId) {
	return getApiUrl(
		`${resource('editions')}/${courseId}?include=${resource('groups')}.${resource('lessons')}.${resource('screens')}.${resource('sections')}`
	)
}

// Initial state
const state = {
	ready: false,
	id: 0,
	name: '',
	groups: [],
	structure: {},
}

// Getters
const getters = {
	courseReady: state => state.ready,
	courseId: state => state.id,
	courseName: state => state.name,
	courseGroups: state => state[resource('groups')],
	courseStructure: state => state.structure,
	getScreens: state => (lessonId) => state.structure[resource('lessons')][lessonId][resource('screens')],
}

// Mutations
const mutations = {
	[types.COURSE_READY] (state) {
		set(state, 'ready', true)
	},
	[types.SET_STRUCTURE] (state, data) {
		set(state, 'id', data.id)
		set(state, 'name', data.name)
		set(state, resource('groups'), data[resource('groups')])
		set(state, 'structure', data.included)
	}
}

// Actions
const actions = {
	courseSetup({ commit, dispatch }, courseId) {
		Promise.all([
			dispatch('courseSetStructure', courseId),
			dispatch('progressSetupCourse', courseId),
		]).then(resolutions => {
			console.log('Course ready, yay!')
			commit(types.COURSE_READY)
		}, reason => {
			console.log(reason)
		})
	},
	courseSetStructure({ commit }, courseId) {
		return new Promise((resolve, reject) => {
			axios.get(getCourseApiUrl(courseId))
				.then((response) => {
					commit(types.SET_STRUCTURE, response.data)
					resolve()
				})
				.catch(error => {
						console.log.bind(console)
						reject()
					}
				)
		})
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
