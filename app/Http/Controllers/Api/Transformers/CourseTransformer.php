<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Course;
use App\Http\Controllers\Api\ApiTransformer;

class CourseTransformer extends ApiTransformer
{
	protected $availableIncludes = ['groups'];

	public function transform(Course $course)
	{
		return [
			'id'   => $course->id,
			'name' => $course->name,
		];
	}

	public function includeGroups(Course $course)
	{
		$groups = $course->groups->sortBy('order_number');

		return $this->collection($groups, new GroupTransformer(), 'groups');
	}
}
