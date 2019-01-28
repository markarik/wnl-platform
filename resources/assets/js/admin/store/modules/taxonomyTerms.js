import {NESTED_SET_EDITOR_MODES} from 'js/consts/nestedSet';
import {nestedSetMutations, nestedSetGetters, nestedSetActions, initialState} from 'js/admin/store/modules/nestedSet';

// Namespace
const namespaced = true;

const state = {...initialState};

const includeTag = (term, {tags}) => {
	term.tag = tags[term.tags[0]];
	return term;
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
		includesParser: includeTag,
		includeResources: 'tags',
		subPath: '/byTaxonomy',
	})
};

export default {
	namespaced,
	state,
	mutations,
	getters,
	actions
};
