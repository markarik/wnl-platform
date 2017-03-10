import axios from 'axios'
import store from 'store' // LocalStorage
import * as types from '../mutations-types'
import { getApiUrl } from 'js/utils/env'
import { set } from 'vue'

// Statuses
// TODO: Mar 9, 2017 - Use config when it's ready
const STATUS_IN_PROGRESS = 'in_progress'
const STATUS_COMPLETE = 'complete'

// Helper functions
function getEditionStoreKey(editionId) {
	return `progress-edition-${editionId}`
}

// API functions
function getUserProgressForEdition(editionId) {
	// return axios.get(getApiUrl('editions/${editionId}/user-progress/${userId}'));
	return new Promise((resolve, reject) => {
		let data = {
				lessons: {
					1: {
						status: STATUS_COMPLETE,
						route: {
							screenId: 4,
							slide: 46
						}
					},
					2: {
						status: STATUS_IN_PROGRESS,
						route: {
							screenId: 5,
							slide: 30
						}
					}
				}
			}
		resolve(data)
	})
}

// Initial state
const state = {
	editions: {}
}

// Getters
const getters = {
	progressWasChecked: (state, editionId) => {
		return state.editions.hasOwnProperty(editionId)
	},
	progressEdition: (state, editionId) => {
		if (state.editions.hasOwnProperty(editionId)) {
			return state.editions[editionId]
		}

		console.log(`Not recognized edition: ${editionId}`)
		return {}
	},
	progressWasLessonStarted: (state) => (editionId, lessonId) => {
		return state.editions.hasOwnProperty(editionId) &&
			state.editions[editionId].hasOwnProperty(lessonId)
	},
	progressIsLessonInProgress: (state) => (editionId, lessonId) => {
		return editions.hasOwnProperty(editionId) &&
			state.editions[editionId].hasOwnProperty(lessonId) &&
			state.editions[editionId][lessonId].status === STATUS_IN_PROGRESS
	},
	progressIsLessonComplete: (state) => (editionId, lessonId) => {
		return editions.hasOwnProperty(editionId) &&
			state.editions[editionId].hasOwnProperty(lessonId) &&
			state.editions[editionId][lessonId].status === STATUS_COMPLETE
	}
}

// Mutations
const mutations = {
	[types.PROGRESS_SETUP_EDITION] (state, payload) {
		set(state.editions, payload.editionId, payload.progressData)
	},
	[types.PROGRESS_START_LESSON] (state, payload) {
		console.log(payload)
		set(state.editions[payload.editionId], payload.lessonId, {
			status: STATUS_IN_PROGRESS
		})
	},
	[types.PROGRESS_UPDATE_LESSON] (state, payload) {
		set(state.editions[payload.editionId][payload.lessonId], 'route', payload.route)
	},
	[types.PROGRESS_COMPLETE_LESSON] (state, editionId, lessonId) {
		set(state.editions[editionId][lessonId], 'status', STATUS_COMPLETE)
	}
}

// Actions
const actions = {
	progressSetupEdition({commit}, editionId) {
		return new Promise((resolve, reject) => {
			let storeKey = getEditionStoreKey(editionId),
				storedProgress = store.get(storeKey)

			if (typeof storedProgress !== 'object') {
				getUserProgressForEdition(editionId)
					.then(data => {
						store.set(storeKey, data)
						commit(types.PROGRESS_SETUP_EDITION, {
							editionId: editionId,
							progressData: data
						})
						resolve()
					})
					.catch(error => console.log(error))
			} else {
				commit(types.PROGRESS_SETUP_EDITION, {
					editionId: editionId,
					progressData: storedProgress
				})
				resolve()
			}
		})
	},
	progressStartLesson({commit, getters}, payload) {
		if (!getters.progressWasLessonStarted(payload.editionId, payload.lessonId)) {
			commit(types.PROGRESS_START_LESSON, payload)
		}
	},
	progressUpdateLesson({commit}, payload) {
		commit(types.PROGRESS_UPDATE_LESSON, payload)
	},
	progressCompleteLesson({commit}, payload) {
		commit(types.PROGRESS_COMPLETE_LESSON, payload)
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
