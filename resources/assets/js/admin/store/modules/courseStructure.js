import {NESTED_SET_EDITOR_MODES} from 'js/consts/nestedSet';
import {nestedSetMutations, nestedSetGetters, nestedSetActions, initialState} from 'js/admin/store/modules/nestedSet';

// Namespace
const namespaced = true;

const state = {...initialState};

const _parseIncludes = (node, included) => {
	if (node.hasOwnProperty('groups')) {
		node.structurable = included.groups[node.groups[0]];
		node.structurable.type = 'App\\Models\\Group';
	}
	if (node.hasOwnProperty('lessons')) {
		node.structurable = included.lessons[node.lessons[0]];
		node.structurable.type = 'App\\Models\\Lesson';
	}
	return node;
};

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
	...nestedSetActions({
		resourceName: 'taxonomy_terms',
		includesParser: _parseIncludes,
		includeResources: 'groups,lessons',
		subPath: '',
	})
};

export default {
	namespaced,
	state,
	mutations,
	getters,
	actions
};
