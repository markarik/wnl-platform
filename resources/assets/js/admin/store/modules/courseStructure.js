import {NESTED_SET_EDITOR_MODES} from 'js/consts/nestedSet';
import {COURSE_STRUCTURE_TYPES} from 'js/consts/courseStructure';
import {nestedSetMutations, nestedSetGetters, nestedSetActions, initialState} from 'js/admin/store/modules/nestedSet';
import axios from 'axios/index';
import {getApiUrl} from 'js/utils/env';

// Namespace
const namespaced = true;

const state = {...initialState};

const _parseIncludes = (node, included) => {
	if (node.hasOwnProperty('groups')) {
		node.structurable = included.groups[node.groups[0]];
		node.structurable.type = COURSE_STRUCTURE_TYPES.GROUP;
	}
	if (node.hasOwnProperty('lessons')) {
		node.structurable = included.lessons[node.lessons[0]];
		node.structurable.type = COURSE_STRUCTURE_TYPES.LESSON;
	}
	return node;
};

const resource = 'course_structure_nodes';
const include = '?include=groups,lessons';

// Getters
const getters = {
	...nestedSetGetters
};

// Mutations
const mutations = {
	...nestedSetMutations
};

// Actions
const actions = {
	...nestedSetActions,
	async _fetch({}, courseId) {
		const {data: {included, ...nodes}} = await axios.get(getApiUrl(`${resource}/${courseId}${include}`));
		return Object.values(nodes).map(node => _parseIncludes(node, included));
	},
	async _post({}, nodeData) {
		const {data: {included, ...node}} = await axios.post(getApiUrl(`${resource}${include}`), nodeData);
		return _parseIncludes(node, included);
	},
	async _put({}, nodeData) {
		const {data: {included, ...node}} = await axios.put(getApiUrl(`${resource}/${nodeData.id}${include}`), nodeData);
		return _parseIncludes(node, included);
	},
	async _delete({}, node) {
		await axios.delete(getApiUrl(`${resource}/${node.id}`));
	},
	async _move({}, {term, direction}) {
		await axios.put(getApiUrl(`${resource}/move`), {id: term.id, direction});
	},
};

export default {
	namespaced,
	state,
	mutations,
	getters,
	actions
};
