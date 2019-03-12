<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\UserLesson;


class UserLessonTransformer extends ApiTransformer
{
	protected $availableIncludes = [];

	public function transform(UserLesson $userLesson)
	{
		$data = [
			'id'                    => $userLesson->id,
			'user_id'               => $userLesson->user_id,
			'lesson_id'             => $userLesson->lesson_id,
			'start_date'            => $userLesson->start_date,
		];

		return $data;
	}
}
