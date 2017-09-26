<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\TagsTaxonomy;
use App\Http\Controllers\Api\ApiTransformer;

class TagsTaxonomyTransformer extends ApiTransformer
{
	public function transform(TagsTaxonomy $edition)
	{
		return [
			'id'   => $edition->id,
			'name' => $edition->name,
		];
	}

	public function includeGroups(Edition $edition)
	{
		$groups = Group::where('course_id', $edition->course_id)
			->orderBy('order_number', 'asc')
			->get();

		return $this->collection($groups, new GroupTransformer($edition->id), 'groups');
	}
}
