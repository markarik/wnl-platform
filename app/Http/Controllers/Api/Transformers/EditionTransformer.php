<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Edition;
use App\Models\Group;
use League\Fractal\TransformerAbstract;

class EditionTransformer extends TransformerAbstract
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
		$groups = Group::where('course_id', $edition->course_id)->get();

		return $this->collection($groups, new GroupTransformer($edition->id), 'groups');
	}
}
