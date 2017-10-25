import _ from 'lodash'
import * as types from '../mutations-types'
import {getApiUrl, envValue as env} from 'js/utils/env'
import {set, delete as destroy} from 'vue'
import pagination from 'js/store/modules/shared/pagination'

const namespaced = true

const state = {
	fetching: false,
	tasks: {}
}

const getters = {
	tasks: (state) => state.tasks
}

const mutations = {
	[types.ADD_TASK] (state, task) {
		set(state.tasks, task.id, task)
	},
	[types.IS_FETCHING] (state, isFetching) {
		set(state, 'fetching', isFetching)
	},
	[types.MODIFY_TASK] (state, payload) {
		set(state, 'tasks', {
			...state.tasks,
			[payload.task.id]: {
				...payload.task,
				[payload.field]: payload.value
			}
		})
	},
}

const actions = {
	pullTasks({commit, dispatch}) {
		commit(types.IS_FETCHING, true)

		return new Promise ((resolve, reject) => {
			_getTasks()
				.then(({data: response}) => {
					const {data, ...paginationMeta} = response;

					// check if response not empty
					if (typeof data[0] !== 'object') {
						dispatch('setPaginationMeta', paginationMeta)
						commit(types.IS_FETCHING, false)
						return resolve(response)
					}

					dispatch('setPaginationMeta', paginationMeta)
					_.each(data, (task) => {
						commit(types.ADD_TASK, task)
					})

					commit(types.IS_FETCHING, false)
					resolve(response)
				})
		})
	},

	setupLiveListener({commit}, channel) {
		Echo.channel(channel)
			.listen('.App.Events.LiveNotificationCreated', (task) => {
				commit(types.ADD_TASK, task)
			});
	},
	initModeratorsFeedListener({getters, dispatch}) {
		dispatch('pullTasks')
		dispatch('setupLiveListener', 'private-group.moderators')
	}
}

const modules = {
	pagination
}

function _getTasks() {
	return axios.get(getApiUrl('tasks/all'), {
		params: {
			limit: 20
		}
	})
}


export default {
	namespaced,
	state,
	mutations,
	getters,
	actions,
	modules
}
