<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Models\Tag;
use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Taggable;

class TaggableTransformer extends ApiTransformer {
	protected $parent;

	public function __construct($parent = null) {
		$this->parent = $parent;
	}

	public function transform(Taggable $taggable) {
		$data = [
			'id' => $taggable->id,
			'tag_id' => $taggable->tag_id,
			'taggable_id' => $taggable->taggable_id,
			'taggable_type' => $taggable->taggable_type,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
