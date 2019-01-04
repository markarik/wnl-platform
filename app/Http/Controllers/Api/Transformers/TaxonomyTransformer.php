<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Taxonomy;

class TaxonomyTransformer extends ApiTransformer {
	protected $parent;

	public function transform(Taxonomy $taxonomy) {
		$data = [
			'id' => $taxonomy->id,
			'name' => $taxonomy->name,
			'description' => $taxonomy->description,
		];

		return $data;
	}
}
