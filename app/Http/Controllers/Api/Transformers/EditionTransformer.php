<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Group;
use App\Models\Edition;
use App\Http\Controllers\Api\ApiTransformer;

class EditionTransformer extends ApiTransformer
{
	protected $availableIncludes = [
		'groups', 'course',
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
		$groups = $edition->course->groups->sortBy('order_number');

		return $this->collection($groups, new GroupTransformer(['editionId' => $edition->id]), 'groups');
	}

	public function includeCourse(Edition $edition)
	{
		$course = $edition->course;

		return $this->item($course, new CourseTransformer(), 'course');
	}
}
