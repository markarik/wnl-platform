import axios from 'axios'
import store from 'store'
import _ from 'lodash'
import { set } from 'vue'
import { useLocalStorage, getApiUrl } from 'js/utils/env'
import { resource } from 'js/utils/config'
import * as types from 'js/store/mutations-types'

const form = {
	namespaced: true,
	state() {
		return {
			data: {},
			defaults: {},
			errors: [],
			method: '',
			originalData: {},
			ready: false,
			resourceUrl: '',
		}
	},
	getters: {
		isReady: (state) => state.ready,
		getData: (state) => state.data,
		getField: (state) => (name) => state.data[name],
	},
	mutations: {
		[types.FORM_SETUP] (state, payload) {
			_.each(payload, (value, key) => {
				set(state, key, value)
			})
		},
		[types.FORM_SET_ORIGINAL_DATA] (state) {
			set(state, 'originalData', state.data)
		},
		[types.FORM_POPULATE] (state, payload) {

		},
		[types.FORM_IS_READY] (state) {
			state.ready = true
		},
		[types.FORM_INPUT] (state, payload) {
			set(state.data, payload.name, payload.value)
		},
		[types.FORM_RESET] (state) {
			_.each(state.data, (field) => {
				set(state.data, field.name, state.defaults[field.name])
			})
		},
	},
	actions: {
		setupForm({commit}, payload) {
			commit(types.FORM_SETUP, payload)
			commit(types.FORM_SET_ORIGINAL_DATA)
		},
		populateForm({commit}, payload) {

		},
		submitForm({state, commit}, payload) {
			let method = payload.method

			if (_.isUndefined(axios[method])) {
				throw `Undefined axios method - ${method}`
			}

			return new Promise((resolve, reject) => {
				axios[method](state.resourceUrl, state.data)
					.then(response => {
						commit(types.FORM_SET_ORIGINAL_DATA)
						resolve(response.data)
					})
					.catch(error => {
						// if (error.response.status === 422) {
						// 	this.errors.record(error.response.data)
						// } else {
							reject(error)
						// }
					})
			})
		},
	},
}

export function createForm() {
	return form
}
