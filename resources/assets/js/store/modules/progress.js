import axios from 'axios'
import store from 'store' // LocalStorage
import * as types from '../mutations-types'
import { getApiUrl } from '../../utils/env'
import { set } from 'vue'

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
						status: 'complete',
						route: {
							screenId: 4,
							slide: 46
						}
					},
					2: {
						status: 'in_progress',
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
	wasProgressChecked: (state, editionId) => {
		return state.editions.hasOwnProperty(editionId)
	},
	editionProgress: (state, editionId) => {
		if (state.editions.hasOwnProperty(editionId)) {
			return state.editions[editionId]
		}

		console.log(`Not recognized edition: ${editionId}`)
		return {}
	}
}

// Mutations
const mutations = {
	[types.PROGRESS_SETUP_EDITION] (state, payload) {
		console.log(payload)
		set(state.editions, payload.editionId, payload.progressData)
	},
	[types.PROGRESS_START_LESSON] (state, editionId, lessonId, route) {
		set(state.editions[editionId], lessonId, {
			status: 'in_progress',
			route: route
		})
	},
	[types.PROGRESS_UPDATE_LESSON] (state, editionId, lessonId, route) {
		set(state.editions[editionId][lessonId], 'route', route)
	},
	[types.PROGRESS_COMPLETE_LESSON] (state, editionId, lessonId) {
		set(state.editions[editionId][lessonId], 'status', 'complete')
	}
}

// Actions
const actions = {
	progressSetupEdition({commit}, editionId) {
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
				})
				.catch(error => console.log(error))
		} else {
			commit(types.PROGRESS_SETUP_EDITION, {
				editionId: editionId,
				progressData: storedProgress
			})
		}
	},
	progressStartLesson({commit}, ) {

	},
	progressUpdateLesson({commit}, ) {

	},
	progressCompleteLesson({commit}, ) {

	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
