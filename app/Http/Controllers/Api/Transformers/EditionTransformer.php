<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Group;
use App\Models\Edition;
use App\Http\Controllers\Api\ApiTransformer;

class EditionTransformer extends ApiTransformer
{
	protected $availableIncludes = [
		'groups',
	];

	public function transform(Edition $edition)
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
