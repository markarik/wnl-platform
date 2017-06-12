import _ from 'lodash'
import store from 'store'
import {set} from 'vue'
import {getApiUrl} from 'js/utils/env'
import {resource} from 'js/utils/config'
import * as types from 'js/store/mutations-types'

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
	id: 0,
	name: '',
	groups: [],
	structure: {},
}

// Getters
const getters = {
	ready: state => state.ready,
	courseId: state => state.id,
	name: state => state.name,
	groups: state => state[resource('groups')],
	structure: state => state.structure,
	getGroup: state => (groupId) => state.structure[resource('groups')][groupId],
	getLessons: state => state.structure[resource('lessons')],
	getAvailableLessons: (state, getters) => {
		let lesson, lessons = []
		for (var lessonId in getters.getLessons) {
			lesson = getters.getLessons[lessonId]
			if (lesson.isAvailable) {
				lessons.push(lesson)
			}
		}
		return lessons
	},
	getLesson: state => (lessonId) => state.structure[resource('lessons')][lessonId],
	isLessonAvailable: state => (lessonId) => state.structure[resource('lessons')][lessonId].isAvailable,
	getScreen: state => (screenId) => state.structure[resource('screens')][screenId],
	getSections: state => ({sections}) => sections.map((sectionId) => structure[resource('sections')][sectionId]),
	getScreens: state => (lessonId) => {
		let screensIds = state.structure[resource('lessons')][lessonId][resource('screens')]

		if (_.isEmpty(screensIds)) {
			return []
		}

		return _.sortBy(
			screensIds.map((screenId) => state.structure[resource('screens')][screenId]),
			'order_number'
		)
	},
	getAdjacentScreenId: (state, getters) => (lessonId, currentScreenId, direction) => {
		let screens = getters.getScreens(lessonId)

		if (_.isEmpty(screens)) return undefined

		let currentScreenIndex = _.findIndex(screens, {'id': parseInt(currentScreenId)}),
			adjScreenIndex

		if (direction === 'next') {
			adjScreenIndex = currentScreenIndex + 1
			if (currentScreenIndex >= 0 && adjScreenIndex < screens.length) {
				return screens[adjScreenIndex].id
			}
		} else if (direction === 'previous') {
			adjScreenIndex = currentScreenIndex - 1
			if (currentScreenIndex > 0) {
				return screens[adjScreenIndex].id
			}
		}

		return undefined
	},
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
	setup({commit, dispatch}, courseId) {
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
	setStructure({commit}, courseId) {
		return new Promise((resolve, reject) => {
			axios.get(getCourseApiUrl(courseId))
				.then((response) => {
					commit(types.SET_STRUCTURE, response.data)
					resolve()
				})
				.catch((exception) => {
						$wnl.logger.capture(exception)
						reject()
					}
				)
		})
	}
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
