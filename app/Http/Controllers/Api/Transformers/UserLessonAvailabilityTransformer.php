<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Models\UserLessonAvailability;
use App\Http\Controllers\Api\ApiTransformer;

class UserLessonAvailabilityTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}


	public function transform(UserLessonAvailability $userLessonAvailability)
	{
		$data = [
			'id'         => $userLessonAvailability->id,
			'user_id'    => $userLessonAvailability->user_id,
			'lesson_id'  => $userLessonAvailability->lesson_id,
			'start_date' => $userLessonAvailability->start_date,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
