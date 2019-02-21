<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Taxonomy;

class TaxonomyTransformer extends ApiTransformer {
	protected $parent;

	public function __construct($parent = null) {
		$this->parent = $parent;
	}

	public function transform(Taxonomy $taxonomy) {
		$data = [
			'id' => $taxonomy->id,
			'name' => $taxonomy->name,
			'description' => $taxonomy->description,
			'color' => $taxonomy->color
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
