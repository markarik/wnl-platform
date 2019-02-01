export function parseTaxonomyTermsFromIncludes(taxonomyTermIds, included) {
	if (!taxonomyTermIds) {
		return [];
	}

	return taxonomyTermIds.map(taxonomyTermId => {
		const taxonomyTerm = included.taxonomy_terms[taxonomyTermId];
		taxonomyTerm.tag = included.tags[taxonomyTerm.tags[0]];
		taxonomyTerm.taxonomy = included.taxonomies[taxonomyTerm.taxonomies[0]];
		taxonomyTerm.ancestors = [];

		let currentTerm = taxonomyTerm;
		while (currentTerm.parent_id) {
			const parentTerm = included.ancestors[currentTerm.parent_id];
			parentTerm.tag = included.tags[parentTerm.tags[0]];
			taxonomyTerm.ancestors.unshift(parentTerm);

			currentTerm = parentTerm;
		}

		return taxonomyTerm;
	});
}
