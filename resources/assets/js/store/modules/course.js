import _ from 'lodash'
import store from 'store'
import {set, delete as destroy} from 'vue'
import {getApiUrl} from 'js/utils/env'
import {resource} from 'js/utils/config'
import * as types from 'js/store/mutations-types'

// Helper functions
function getCourseApiUrl(courseId, userId) {
	return getApiUrl(
		`${resource('editions')}/${courseId}
		?include=groups.lessons.screens.sections.subsections,
		course.groups.lessons.screens.sections.subsections,
		course.groups.lessons.screens.tags
		&user=current`
	)
}

const STATUS_NONE = 'none'
const STATUS_IN_PROGRESS = 'in-progress'
const STATUS_AVAILABLE = 'available'

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
	getGroup: state => (groupId) => state.structure[resource('groups')][groupId] || {},
	getLessons: state => state.structure[resource('lessons')] || {},
	userLessons: (state, getters, rootState, rootGetters) => {
		return Object.values(getters.getLessons)
			.filter(lesson => lesson.isAccessible);
	},
	getLesson: state => (lessonId) => _.get(state.structure[resource('lessons')], lessonId, {}),
	getLessonByName: state => (name) => _.filter(state.structure[resource('lessons')], (lesson) => lesson.name === name),
	isLessonAvailable: (state, getters, rootState, rootGetters) => (lessonId) => {
		return state.structure[resource('lessons')][lessonId].isAvailable
	},
	isLessonAccessible: (state, getters, rootState, rootGetters) => (lessonId) => {
		return state.structure[resource('lessons')][lessonId].isAccessible
	},
	getScreen: state => (screenId) => state.structure[resource('screens')][screenId],
	getSection: state => (sectionId) => _.get(state.structure['sections'], sectionId, {}),
	getSections: state => (sections) => sections.map((sectionId) => _.get(state.structure, `sections.${sectionId}`, {})) || [],
	getSubsections: state => (subsections) => subsections.map((subsectionId) => _.get(state.structure, `subsections.${subsectionId}`, {})) || [],
	getScreenSectionsCheckpoints: (state, getters) => (screenId) => {
		const sectionsIds = getters.getScreen(screenId).sections;
		const sections = getters.getSections(sectionsIds);

		return sections.map((section) => section.slide);
	},
	getSectionSubsectionsCheckpoints: (state, getters) => (sectionId) => {
		const subsectionsIds = getters.getSection(sectionId).subsections;
		const subsections = getters.getSubsections(subsectionsIds);

		return subsections.map((subsections) => subsections.slide);
	},
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
	nextLesson: (state, getters, rootState, rootGetters) => {
		if (typeof getters.getLessons === 'undefined' || !rootGetters['progress/getCourse'](state.id)) {
			return {}
		}

		const inProgressId = rootGetters['progress/getFirstLessonIdInProgress'](state.id)

		if (inProgressId > 0) {
			const lesson = getters.getLesson(inProgressId)
			lesson.status = STATUS_IN_PROGRESS

			return lesson;
		} else {
			const sortedLessonsIds = Object.keys(getters.getLessons).sort((keyA, keyB) => {
				const lessonA = getters.getLessons[keyA]
				const lessonB = getters.getLessons[keyB]

				const byOrderNumber = lessonA.order_number - lessonB.order_number
				if (byOrderNumber === 0) {
					return lessonA.id - lessonB.id
				}
				return byOrderNumber
			}).map(Number)

			for (let i = 0; i < sortedLessonsIds.length; i++) {
				const lessonId = sortedLessonsIds[i];
				const isAvailable = getters.isLessonAvailable(lessonId)
				const isAccessible = getters.isLessonAccessible(lessonId)
				if (isAvailable &&
					!rootGetters['progress/wasLessonStarted'](state.id, lessonId)
				) {
					const lesson = getters.getLesson(lessonId)
					lesson.status = STATUS_AVAILABLE
					return lesson
				} else if (!isAvailable && isAccessible) {
					const lesson = getters.getLesson(lessonId)
					lesson.status = STATUS_NONE
					return lesson
				}
			}
		}

		return {
			status: STATUS_NONE
		}
	}
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
	},
	[types.COURSE_REMOVE_GROUP] (state, payload) {
		state.groups.splice(payload.index, 1)
		destroy(state.structure.groups, payload.id)
		payload.lessons.forEach((lesson) => {
			destroy(state.structure.lessons, lesson)
		})
	},
	[types.COURSE_SET_LESSON_AVAILABILITY] (state, payload) {
		set(state.structure.lessons[payload.lessonId], 'isAvailable', payload.status)
	}
}

// Actions
const actions = {
	setup({commit, dispatch, rootGetters}, courseId) {
		return new Promise((resolve, reject) => {
			Promise.all([
				dispatch('setStructure', courseId),
				dispatch('progress/setupCourse', courseId, {root: true}),
			])
			.then(resolutions => {
				$wnl.logger.debug('Course ready, yay!')
				commit(types.COURSE_READY)
				return resolve()
			}, reason => {
				commit(types.COURSE_READY)
				$wnl.logger.error(reason)
				return reject()
			}).catch(reject)
		})
	},
	setStructure({commit, rootGetters}, courseId = 1) {
		return new Promise((resolve, reject) => {
			axios.get(getCourseApiUrl(courseId, rootGetters.currentUserId))
				.then(response => {
					commit(types.SET_STRUCTURE, response.data)
					resolve()
				})
				.catch(exception => {
						$wnl.logger.capture(exception)
						reject()
					}
				)
		})
	},
	setLessonAvailabilityStatus({commit}, payload) {
		commit(types.COURSE_SET_LESSON_AVAILABILITY, payload)
	},
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
