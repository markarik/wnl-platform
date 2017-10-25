import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import {set} from 'vue'
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
	[types.SET_TASKS] (state, tasks) {
		set(state, 'tasks', tasks)
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
	pullTasks({commit, dispatch}, {params} = {}) {
		commit(types.IS_FETCHING, true)

		return new Promise ((resolve, reject) => {
			_getTasks(params)
				.then(({data: response}) => {
					const {data, ...paginationMeta} = response;

					// check if response not empty
					if (typeof data[0] !== 'object') {
						dispatch('setPaginationMeta', paginationMeta)
						commit(types.IS_FETCHING, false)
						return resolve(response)
					}

					dispatch('setPaginationMeta', paginationMeta)

					const serializedTasks = {}
					data.forEach(task => serializedTasks[task.id] = task)

					commit(types.SET_TASKS, serializedTasks)
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

function _getTasks(params) {
	return axios.get(getApiUrl('tasks/all'), {
		params: {
			limit: 2,
			...params
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
