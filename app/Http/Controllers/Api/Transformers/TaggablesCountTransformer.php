<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Tag;
use App\Http\Controllers\Api\ApiTransformer;


class TaggablesCountTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Tag $tag)
	{
		$data = [
			'taggables_count' => $tag->taggables_count,
			'id' => $tag->id,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
