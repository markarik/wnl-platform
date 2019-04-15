import { nestedSetMutations, nestedSetGetters, nestedSetActions, initialState } from 'js/store/modules/shared/nestedSet';
import axios from 'axios';
import { getApiUrl } from 'js/utils/env';

// Namespace
const namespaced = true;

const state = { ...initialState };

const includeTag = (term, { tags }) => {
	term.tag = { ...tags[term.tag[0]] };
	return term;
};

const resource = 'taxonomy_terms';
const include = '?include=tag';

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
	async _fetch(_, taxonomyId) {
		const { data: { included, ...terms } } = await axios.get(getApiUrl(`${resource}/byTaxonomy/${taxonomyId}${include}`));
		return Object.values(terms).map(term => includeTag(term, included));
	},
	async _post(_, termData) {
		const { data: { included, ...term } } = await axios.post(getApiUrl(`${resource}${include}`), termData);
		return includeTag(term, included);
	},
	async _put(_, termData) {
		const { data: { included, ...term } } = await axios.put(getApiUrl(`${resource}/${termData.id}${include}`), termData);
		return includeTag(term, included);
	},
	async _delete(_, term) {
		await axios.delete(getApiUrl(`${resource}/${term.id}`));
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
