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
		course.groups.lessons.userAccess,course.groups.lessons.availability,
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
	getLessons: state => state.structure[resource('lessons')],
	getAvailableLessons: (state, getters, rootState, rootGetters) => {
		if (rootGetters.isAdmin) {
			return _.values(getters.getLessons)
		}

		let lesson, lessons = []
		for (var lessonId in getters.getLessons) {
			lesson = getters.getLessons[lessonId]
			if (lesson.isAvailable) {
				lessons.push(lesson)
			}
		}
		return lessons
	},
	getLesson: state => (lessonId) => state.structure[resource('lessons')][lessonId] || {},
	getLessonByName: state => (name) => _.filter(state.structure[resource('lessons')], (lesson) => lesson.name === name),
	isLessonAvailable: (state, getters, rootState, rootGetters) => (lessonId) => {
		return rootGetters.isAdmin || state.structure[resource('lessons')][lessonId].isAvailable
	},
	getScreen: state => (screenId) => state.structure[resource('screens')][screenId],
	getSection: state => (sectionId) => state.structure['sections'][sectionId] || {},
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
			return false
		}

		let lesson = { status: STATUS_NONE },
			inProgressId = rootGetters['progress/getFirstLessonIdInProgress'](state.id)

		if (inProgressId > 0) {
			lesson = getters.getLesson(inProgressId)
			lesson.status = STATUS_IN_PROGRESS
		} else {
			const sortedLessonsIds = Object.keys(getters.getLessons).sort((keyA, keyB) => {
				const lessonA = getters.getLessons[keyA]
				const lessonB = getters.getLessons[keyB]

				return lessonA.order_number - lessonB.order_number
			}).map(Number)

			for (let i = 0; i < sortedLessonsIds.length; i++) {
				let lessonId = sortedLessonsIds[i];
				let isAvailable = getters.isLessonAvailable(lessonId)
				if (isAvailable &&
					!rootGetters['progress/wasLessonStarted'](state.id, lessonId)
				) {
					lesson = getters.getLesson(lessonId)
					lesson.status = STATUS_AVAILABLE
					return lesson
				} else if (!isAvailable) {
					lesson = getters.getLesson(lessonId)
					lesson.status = STATUS_NONE
					return lesson
				}
			}
		}

		return lesson
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
}

// Actions
const actions = {
	setup({commit, dispatch}, courseId) {
		return new Promise((resolve, reject) => {
			Promise.all([
				dispatch('setStructure', courseId),
				dispatch('progress/setupCourse', courseId, {root: true}),
			])
			.then(resolutions => {
				$wnl.logger.debug('Course ready, yay!')
				commit(types.COURSE_READY)
				resolve()
			}, reason => {
				$wnl.logger.error(reason)
				reject()
			})
		})
	},
	setStructure({commit, rootGetters}, courseId) {
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
	checkUserRoles({commit, dispatch, getters}, roles) {
		return new Promise((resolve, reject) => {
			let toRemove = []

			Object.keys(getters.groups).forEach((index) => {
				let id = getters.groups[index],
					group = getters.getGroup(id)
				if (!group.required_role) {
					return
				}

				if (roles.indexOf(group.required_role) === -1) {
					toRemove.push({index, id, lessons: group.lessons})
				}
			})

			toRemove.forEach((payload) => {
				commit(types.COURSE_REMOVE_GROUP, payload)
			})
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
