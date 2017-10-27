import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import {set} from 'vue'
import pagination from 'js/store/modules/shared/pagination'
import profiles from 'js/store/modules/shared/profiles'

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
	[types.MODIFY_TASK] (state, task) {
		Object.assign(state.tasks[task.id], task)
	},
}

const actions = {
	pullTasks({commit, dispatch}, {params} = {}) {
		commit(types.IS_FETCHING, true)

		return new Promise ((resolve, reject) => {
			_getTasks(params)
				.then(({data: response}) => {
					const {data, ...paginationMeta} = response;
					const {included: allIncluded, ...responseData} = data;
					const {profiles = {}, ...included} = allIncluded

					const dataArray = Object.values(responseData);

					// check if response not empty
					if (typeof dataArray[0] !== 'object') {
						dispatch('setPaginationMeta', paginationMeta)
						commit(types.IS_FETCHING, false)
						return resolve(response)
					}

					dispatch('setPaginationMeta', paginationMeta)

					const serializedTasks = {}
					dataArray.forEach(task => {
						serializedTasks[task.id] = _parseIncludes(included, task)
						serializedTasks[task.id].assignee = profiles[task.assignee_id] || {}
					});

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
	},
	updateTask({commit}, payload) {
		_updateTask(payload)
			.then(({data: {included: allIncluded, ...task}}) => {
				const {profiles = {}, ...included} = allIncluded

				const assignee = {assignee: profiles[task.assignee_id] || {}};

				Object.assign(task, _parseIncludes(included, task), assignee)

				commit(types.MODIFY_TASK, task)
			}).catch(() => {
				debugger
				// dispatch notification with error
			})
	}
}

const modules = {
	pagination,
	profiles
}

function _getTasks(params) {
	return axios.get(getApiUrl('tasks/all'), {
		params: {
			limit: 2,
			include: 'events,profiles',
			...params
		}
	})
}

function _updateTask({id, ...fields}) {
	return axios.patch(getApiUrl(`tasks/${id}?include=events,profiles`), fields)
}

function _parseIncludes(included, object) {
	const updatedObject = {...object};

	Object.keys(included).forEach((include) => {
		if (updatedObject[include]) {
			updatedObject[include] = updatedObject[include].map((id) => included[include][id])
		}
	});

	return updatedObject
}


export default {
	namespaced,
	state,
	mutations,
	getters,
	actions,
	modules
}
