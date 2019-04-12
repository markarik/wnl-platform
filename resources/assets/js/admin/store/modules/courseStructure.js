import { COURSE_STRUCTURE_TYPES } from 'js/consts/courseStructure';
import { nestedSetMutations, nestedSetGetters, nestedSetActions, initialState } from 'js/store/modules/shared/nestedSet';
import axios from 'axios';
import { getApiUrl } from 'js/utils/env';
import { COURSE_STRUCTURE_TYPE_ICONS } from 'js/consts/courseStructure';

// Namespace
const namespaced = true;

const state = { ...initialState };

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
	...nestedSetGetters,
	getStructurableIcon: () => structurable => {
		return COURSE_STRUCTURE_TYPE_ICONS[structurable.type];
	}
};

// Mutations
const mutations = {
	...nestedSetMutations
};

// Actions
const actions = {
	...nestedSetActions,
	async _fetch(_, courseId) {
		const { data: { included, ...nodes } } = await axios.get(getApiUrl(`${resource}/${courseId}${include}`));
		return Object.values(nodes).map(node => _parseIncludes(node, included));
	},
	async _post(_, nodeData) {
		const { data: { included, ...node } } = await axios.post(getApiUrl(`${resource}${include}`), nodeData);
		return _parseIncludes(node, included);
	},
	async _put(_, nodeData) {
		const { data: { included, ...node } } = await axios.put(getApiUrl(`${resource}/${nodeData.id}${include}`), nodeData);
		return _parseIncludes(node, included);
	},
	async _delete(_, node) {
		await axios.delete(getApiUrl(`${resource}/${node.id}`));
	},
	async _move(_, { node, direction }) {
		await axios.put(getApiUrl(`${resource}/move`), { id: node.id, direction });
	},
};

export default {
	namespaced,
	state,
	mutations,
	getters,
	actions
};
