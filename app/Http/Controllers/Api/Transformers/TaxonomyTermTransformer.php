<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Http\Controllers\Api\ApiTransformer;
use App\Models\TaxonomyTerm;

class TaxonomyTermTransformer extends ApiTransformer {
	protected $parent;
	protected $availableIncludes = ['tags', 'taxonomies', 'ancestors'];

	public function __construct($parent = null) {
		$this->parent = $parent;
	}

	public function transform(TaxonomyTerm $taxonomyTerm) {
		$data = [
			'id' => $taxonomyTerm->id,
			'description' => $taxonomyTerm->description,
			'parent_id' => $taxonomyTerm->parent_id,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeTags(TaxonomyTerm $taxonomyTerm)
	{
		return $this->item($taxonomyTerm->tag, new TagTransformer(['taxonomy_terms' => $taxonomyTerm->id]), 'tags');
	}

	public function includeTaxonomies(TaxonomyTerm $taxonomyTerm)
	{
		return $this->item($taxonomyTerm->taxonomy, new TaxonomyTransformer(['taxonomy_terms' => $taxonomyTerm->id]), 'taxonomies');
	}

	public function includeAncestors(TaxonomyTerm $taxonomyTerm)
	{
		return $this->collection($taxonomyTerm->ancestors, new TaxonomyTermTransformer(['taxonomy_terms' => $taxonomyTerm->id]), 'taxonomy_terms');
	}
}
