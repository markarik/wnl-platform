<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Course;
use App\Models\Edition;
use App\Http\Controllers\Api\ApiTransformer;

class CourseTransformer extends ApiTransformer
{
	public function transform(Course $course)
	{
		return [
			'id'   => $course->id,
			'name' => $course->name,
		];
	}
}
