import Vue from 'vue';
import Vuex from 'vuex';

// Global mutations, actions and getters
import mutations from 'js/admin/store/mutations';
import * as actions from 'js/admin/store/actions';
import * as getters from 'js/admin/store/getters';

// Modules
import currentUser from 'js/store/modules/currentUser';
import lessons from 'js/admin/store/modules/lessons';
import notifications from 'js/store/modules/notifications';
import quiz from 'js/admin/store/modules/quiz';
import autocomplete from 'js/store/modules/autocomplete';
import alerts from 'js/store/modules/alerts';
import flashcards from 'js/admin/store/modules/flashcards';
import form from 'js/store/modules/form';
import flashcardsSets from 'js/admin/store/modules/flashcardsSets';
import taxonomyTerms from 'js/store/modules/taxonomyTerms';
import tags from 'js/admin/store/modules/tags';
import courseStructure from 'js/admin/store/modules/courseStructure';
import groups from './modules/groups';
import taxonomies from 'js/store/modules/taxonomies';
import contentClassifier from 'js/store/modules/contentClassifier';

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
	state: {},
	getters,
	mutations,
	actions,
	modules: {
		currentUser,
		notifications,
		lessons,
		groups,
		quiz,
		autocomplete,
		alerts,
		flashcards,
		flashcardsSets,
		form,
		taxonomyTerms,
		taxonomies,
		tags,
		courseStructure,
		contentClassifier
	},
	strict: debug
});
