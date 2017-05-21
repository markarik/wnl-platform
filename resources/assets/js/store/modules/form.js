import axios from 'axios'
import store from 'store'
import _ from 'lodash'
import { set, delete as destroy } from 'vue'
import { useLocalStorage, getApiUrl } from 'js/utils/env'
import { resource } from 'js/utils/config'
import * as types from 'js/store/mutations-types'

let originalData = {}

const form = {
	namespaced: true,
	state() {
		return {
			data: {},
			defaults: {},
			errors: {},
			hasChanges: false,
			loading: true,
			method: '',
			original: {},
			resourceUrl: '',
		}
	},
	getters: {
		anyErrors:  (state) => !_.isEmpty(state.errors),
		getData:    (state) => state.data,
		getErrors:  (state) => (name) => state.errors[name],
		getField:   (state) => (name) => state.data[name],
		hasChanges: (state) => state.hasChanges,
		hasErrors:  (state) => (name) => !_.isEmpty(state.errors[name]),
		isLoading:  (state) => state.loading,
	},
	mutations: {
		[types.FORM_SETUP] (state, payload) {
			_.each(payload, (value, key) => {
				set(state, key, value)
			})
		},
		[types.FORM_UPDATE_ORIGINAL_DATA] (state) {
			_.each(state.data, (value, key) => {
				state.original[key] = value
			})
			set(state, 'hasChanges', false)
		},
		[types.FORM_POPULATE] (state, payload) {
			_.each(payload, (value, name) => {
				set(state.data, name, value)
			})
		},
		[types.FORM_IS_LOADING] (state) {
			set(state, 'loading', true)
		},
		[types.FORM_IS_LOADED] (state) {
			set(state, 'loading', false)
		},
		[types.FORM_INPUT] (state, payload) {
			set(state.data, payload.name, payload.value)
			set(state, 'hasChanges', true)
		},
		[types.FORM_RESET] (state) {
			_.each(state.data, (field) => {
				set(state.data, field.name, state.defaults[field.name])
			})
		},
		[types.ERRORS_RECORD] (state, payload) {
			set(state, 'errors', payload)
		},
		[types.ERRORS_CLEAR_SINGLE] (state, payload) {
			destroy(state.errors, payload.name)
		},
		[types.ERRORS_CLEAR] (state, payload) {
			set(state, 'errors', {})
		},
	},
	actions: {
		setupForm({commit}, payload) {
			commit(types.FORM_SETUP, payload)
		},
		populateForm({state, commit}) {
			return axios.get(state.resourceUrl)
				.then((response) => {
					commit(types.FORM_POPULATE, response.data)
					commit(types.FORM_UPDATE_ORIGINAL_DATA)
				})
				.catch((error) => {
					$wnl.logger.error(error)
				})
		},
		submitForm({state, commit}, payload) {
			let method = payload.method

			if (_.isUndefined(axios[method])) {
				throw `Undefined axios method - ${method}`
			}

			commit(types.FORM_IS_LOADING)

			let data = state.data

			if (!_.isEmpty(payload.attach)) {
				data = _.merge(state.data, payload.attach)
			}

			return new Promise((resolve, reject) => {
				axios[method](state.resourceUrl, data)
					.then(response => {
						commit(types.FORM_UPDATE_ORIGINAL_DATA)
						commit(types.FORM_IS_LOADED)
						resolve(response.data)
					})
					.catch(error => {
						commit(types.FORM_IS_LOADED)

						if (error.response.status === 422) {
							commit(types.ERRORS_RECORD, error.response.data)
							reject(error)
						} else {
							reject(error)
						}
					})
			})
		},
	},
}

export function createForm() {
	return form
}
