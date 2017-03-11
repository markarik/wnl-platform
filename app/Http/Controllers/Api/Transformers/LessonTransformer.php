<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Lesson;
use League\Fractal\TransformerAbstract;

class LessonTransformer extends TransformerAbstract
{
	protected $availableIncludes = [];

	public function transform(Lesson $lesson)
	{
		return [
			'id'    => $lesson->id,
			'name'  => $lesson->name,
			'group' => $lesson->group_id,
		];
	}
}