import axios from 'axios';

import {CONTENT_TYPE_TO_RESOURCE_ROUTE} from 'js/consts/contentClassifier';
import {getApiUrl} from 'js/utils/env';
import {parseTaxonomyTermsFromIncludes} from 'js/utils/contentClassifier';

const INCLUDE = 'taxonomy_terms.tags,taxonomy_terms.taxonomies,taxonomy_terms.ancestors.tags';

const actions = {
	async fetchTaxonomyTerms({commit}, {contentType, contentId}) {
		const url = getApiUrl(`${CONTENT_TYPE_TO_RESOURCE_ROUTE[contentType]}/${contentId}`);
		const response = await axios.get(url, {
			params: {
				include: INCLUDE
			}
		});
		const {included, ...contentItem} = response.data;
		const taxonomyTerms = parseTaxonomyTermsFromIncludes(contentItem.taxonomy_terms, included);

		return {
			...contentItem,
			type: contentType,
			taxonomyTerms
		};
	}
};

export default {
	actions,
	namespaced: true
};
