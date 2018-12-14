<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\TagsTaxonomy;
use App\Http\Controllers\Api\ApiTransformer;

class TagsTaxonomyTransformer extends ApiTransformer
{
	public function transform(TagsTaxonomy $tagsTaxonomy)
	{
		return [
			'id'   => $tagsTaxonomy->id,
			'name' => $tagsTaxonomy->name,
		];
	}
}
