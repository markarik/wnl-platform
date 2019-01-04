<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Models\Tag;
use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Taxonomy;

class TaxonomyTransformer extends ApiTransformer {
	protected $parent;

	public function transform(Taxonomy $taxonomy) {
		$data = [
			'id' => $taxonomy->id,
			'name' => $taxonomy->name,
		];

		return $data;
	}
}
