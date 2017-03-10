import store from 'store'
import { set } from 'vue'
import { getApiUrl } from 'js/utils/env'
import { resource } from 'js/utils/config'
import * as types from 'js/store/mutations-types'

// Helper functions
function getStoreKey(editionId) {
	return `edition-structure-${editionId}`
}

function getEditionApiUrl(editionId) {
	return getApiUrl(`${resource('courses')}/${editionId}/nav`)
}

// Initial state
const state = {
	id: 0,
	name: '',
	groups: [],
	structure: {},
}

// Getters
const getters = {
	courseId: state => state.id,
	courseName: state => state.name,
	courseGroups: state => state[resource('groups')],
	courseStructure: state => state.structure,
	getFirstScreen: state => (lessonId) => {
		let screenId = state.structure[resource('lessons')][lessonId][resource('screens')][0]
		return state.structure[resource('screens')][screenId]
	}
}

// Mutations
const mutations = {
	[types.SET_STRUCTURE] (state, data) {
		set(state, 'id', data.id)
		set(state, 'name', data.name)
		set(state, resource('groups'), data[resource('groups')])
		set(state, 'structure', data.structure)
	}
}

// Actions
const actions = {
	setStructure({ commit }, editionId) {
		let storeKey = getStoreKey(editionId),
			storedData = store.get(storeKey)

		if (typeof storedData !== 'object') {
			axios.get(getEditionApiUrl(editionId)).then((response) => {
				// store.
				commit(types.SET_STRUCTURE, response.data)
			}).catch(console.log.bind(console))
		} else {
			commit(types.SET_STRUCTURE, storedData)
		}
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
