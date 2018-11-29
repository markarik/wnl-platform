import axios from 'axios'
import _ from 'lodash'
import {delete as destroy, set} from 'vue'
import * as types from 'js/store/mutations-types'

const INITIAL_STATE = {
	data: {},
	defaults: {},
	errors: {},
	hasChanges: false,
	loading: true,
	method: '',
	original: {},
	resourceUrl: '',
}

export default {
	namespaced: true,
	state() {
		return {}
	},
	getters: {
		anyErrors:   (state) => formName => !_.isEmpty(state[formName].errors),
		getData:     (state) => formName => state[formName].data,
		getOriginal: (state) => formName => state[formName].original,
		getErrors:   (state) => formName => name => state[formName].errors[name],
		getField:    (state) => formName => name => state[formName].data[name],
		hasChanges:  (state) => formName => state[formName].hasChanges,
		hasErrors:   (state) => formName => name => !_.isEmpty(state[formName].errors[name]),
		isLoading:   (state) => formName => state[formName].loading,
	},
	mutations: {
		[types.FORM_INITIAL_SETUP] (state, {formName}) {
			set(state, formName, {...INITIAL_STATE});
		},
		[types.FORM_SETUP] (state, {payload, formName}) {
			_.each(payload, (value, key) => {
				set(state[formName], key, value)
			})
		},
		[types.FORM_UPDATE_ORIGINAL_DATA] (state, {formName}) {
			set(state[formName], 'original', _.cloneDeep(state[formName].data))
			set(state[formName], 'hasChanges', false)
		},
		[types.FORM_UPDATE_URL] (state, {payload, formName}) {
			set(state[formName], 'resourceUrl', payload)
		},
		[types.FORM_POPULATE] (state, {payload, formName}) {
			_.each(payload, (value, name) => {
				set(state[formName].data, name, value)
			})
		},
		[types.FORM_IS_LOADING] (state, {formName}) {
			set(state[formName], 'loading', true)
		},
		[types.FORM_IS_LOADED] (state, {formName}) {
			set(state[formName], 'loading', false)
		},
		[types.FORM_INPUT] (state, {payload, formName}) {
			set(state[formName].data, payload.name, payload.value)
			set(state[formName], 'hasChanges', true)
		},
		[types.FORM_RESET] (state, {formName}) {
			destroy(state[formName].data)
			_.each(state[formName].defaults, (value, field) => {
				set(state[formName].data, field, state[formName].defaults[field])
			})
			set(state[formName], 'hasChanges', false)
		},
		[types.ERRORS_RECORD] (state, {payload, formName}) {
			set(state[formName], 'errors', payload)
		},
		[types.ERRORS_CLEAR_SINGLE] (state, {payload, formName}) {
			destroy(state[formName].errors, payload.name)
		},
		[types.ERRORS_CLEAR] (state, {formName}) {
			set(state[formName], 'errors', {})
		},
	},
	actions: {
		setupForm({commit}, payload) {
			commit(types.FORM_SETUP, payload)
		},
		populateFormFromApi({state, commit}, {formName}) {
			return axios.get(state[formName].resourceUrl)
				.then((response) => {
					commit(types.FORM_POPULATE, {payload: response.data, formName})
					commit(types.FORM_UPDATE_ORIGINAL_DATA, {formName})
				})
				.catch((error) => {
					$wnl.logger.error(error)
				})
		},
		populateFormFromValue({state, commit}, {payload, formName}) {
			commit(types.FORM_POPULATE, {payload, formName})
			commit(types.FORM_UPDATE_ORIGINAL_DATA, {formName})
		},
		submitForm({state, commit}, {payload, formName}) {
			let method = payload.method

			if (_.isUndefined(axios[method])) {
				throw `Undefined axios method - ${method}`
			}

			commit(types.FORM_IS_LOADING, {formName})

			let data = state[formName].data

			if (!_.isEmpty(payload.attach)) {
				data = _.merge(state[formName].data, payload.attach)
			}

			return new Promise((resolve, reject) => {
				axios[method](state[formName].resourceUrl, data)
					.then(response => {
						commit(types.FORM_UPDATE_ORIGINAL_DATA, {formName})
						commit(types.FORM_IS_LOADED, {formName})
						resolve(response.data)
					})
					.catch(error => {
						commit(types.FORM_IS_LOADED, {formName})

						if (error.response.status === 422) {
							commit(types.ERRORS_RECORD, {payload: error.response.data.errors, formName})
							reject(error)
						} else {
							reject(error)
						}
					})
			})
		},
	},
}
