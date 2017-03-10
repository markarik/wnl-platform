import store from 'store'
import { set } from 'vue'
import { getApiUrl } from 'js/utils/env'
import * as types from 'js/store/mutations-types'

// Helper functions
function getStoreKey(editionId) {
	return `edition-structure-${editionId}`
}

function getEditionApiUrl(editionId) {
	return getApiUrl(`courses/${editionId}/nav`)
}

// Initial state
const state = {
	id: 0,
	name: '',
	structure: {},
}

// Getters
const getters = {
	courseId: state => state.id,
	courseName: state => state.name,
	courseStructure: state => state.structure,
}

// Mutations
const mutations = {
	[types.SET_STRUCTURE] (state, data) {
		set(state, 'id', data.id)
		set(state, 'name', data.name)
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
