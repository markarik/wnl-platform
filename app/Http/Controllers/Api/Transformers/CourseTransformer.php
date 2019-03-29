<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Course;
use App\Http\Controllers\Api\ApiTransformer;

class CourseTransformer extends ApiTransformer
{
	protected $availableIncludes = ['groups'];
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Course $course)
	{
		$data = [
			'id'                      => $course->id,
			'name'                    => $course->name,
			'is_plan_builder_enabled' => $course->is_plan_builder_enabled
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeGroups(Course $course)
	{
		$groups = $course->groups;

		return $this->collection($groups, new GroupTransformer(['courses' => $course->id]), 'groups');
	}
}
