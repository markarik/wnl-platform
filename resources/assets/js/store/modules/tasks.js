import axios from 'axios';
import * as types from 'js/store/mutations-types';
import { getApiUrl } from 'js/utils/env';
import { set } from 'vue';
import pagination from 'js/store/modules/shared/pagination';
import profiles from 'js/store/modules/shared/profiles';
import { isEmpty } from 'lodash';

const namespaced = true;

const state = {
	fetching: false,
	tasks: {},
	updatedTasks: [],
};

const getters = {
	tasks: (state) => state.tasks,
	updatedTasks: state => state.updatedTasks
};

const mutations = {
	[types.ADD_TASK] (state, task) {
		set(state, 'updatedTasks', [...state.updatedTasks, task]);
	},
	[types.SET_TASKS] (state, tasks) {
		set(state, 'tasks', tasks);
		set(state, 'updatedTasks', []);
	},
	[types.IS_FETCHING] (state, isFetching) {
		set(state, 'fetching', isFetching);
	},
	[types.MODIFY_TASK] (state, task) {
		Object.assign(state.tasks[task.id], task);
	},
};

const actions = {
	pullTasks({ commit, dispatch }, params) {
		commit(types.IS_FETCHING, true);

		return new Promise ((resolve) => {
			_getTasks(params)
				.then(({ data: response }) => {
					_handleResponse({ commit,dispatch }, response, resolve);
				});
		});
	},
	setupLiveListener({ commit }, channel) {
		Echo.channel(channel)
			.listen('.App.Events.Live.LiveNotificationCreated', (task) => {
				commit(types.ADD_TASK, task);
			});
	},
	initModeratorsFeedListener({ dispatch }) {
		dispatch('setupLiveListener', 'private-group.moderators');
	},
	updateTask({ commit, dispatch }, payload) {
		_updateTask(payload)
			.then(({ data: { included: allIncluded, ...task } }) => {
				const { assigneeProfiles = {}, ...included } = allIncluded;
				const taskProfile = task.assigneeProfiles || [];
				const assignee = { assignee: assigneeProfiles[taskProfile[0]] || null };

				Object.assign(task, _parseIncludes(included, task), assignee);

				commit(types.MODIFY_TASK, task);
			}).catch(error => {
				dispatch('addAlert', { type: 'error', text: 'Nie udało się zapisać. Odśwież stronę i spróbuj ponownie' }, { root: true });
				$wnl.logger.error(error);
			});
	}
};

const modules = {
	pagination,
	profiles
};

function _getTasks(params) {
	return axios.post(getApiUrl('tasks/.filter'), {
		limit: 10,
		include: 'events,assigneeProfiles',
		...params
	});
}

function _updateTask({ id, ...fields }) {
	return axios.patch(getApiUrl(`tasks/${id}?include=events,assigneeProfiles`), fields);
}

function _parseIncludes(included, object) {
	const updatedObject = { ...object };

	Object.keys(included).forEach((include) => {
		if (updatedObject[include]) {
			updatedObject[include] = updatedObject[include].map((id) => included[include][id]);
		}
	});

	return updatedObject;
}

function _handleResponse({ commit, dispatch }, response, resolve) {
	const { data, ...paginationMeta } = response;
	if (isEmpty(data)) {
		commit(types.SET_TASKS, {});
		commit(types.IS_FETCHING, false);

		return resolve(response);
	}

	const { included: allIncluded, ...responseData } = data;
	const { assigneeProfiles = {}, ...included } = allIncluded;

	const dataArray = Object.values(responseData);

	// check if response not empty
	if (typeof dataArray[0] !== 'object') {
		dispatch('setPaginationMeta', paginationMeta);
		commit(types.IS_FETCHING, false);
		return resolve(response);
	}

	dispatch('setPaginationMeta', paginationMeta);

	const serializedTasks = {};
	dataArray.forEach(task => {
		serializedTasks[task.id] = _parseIncludes(included, task);
		serializedTasks[task.id].assignee = Object.values(assigneeProfiles).find(assigneeProfile => {
			return assigneeProfile.user_id === task.assignee_id;
		}) || null;
	});

	commit(types.SET_TASKS, serializedTasks);
	commit(types.IS_FETCHING, false);

	resolve(response);
}

export default {
	namespaced,
	state,
	mutations,
	getters,
	actions,
	modules
};
