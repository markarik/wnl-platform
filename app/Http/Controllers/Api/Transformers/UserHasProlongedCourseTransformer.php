<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Http\Controllers\Api\ApiTransformer;
use App\Models\UserProfile;

class UserHasProlongedCourseTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserProfile $userProfile)
	{
		$data = [
			'id' => $userProfile->id,
			'has_prolonged_course' => $userProfile->user->has_prolonged_course
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
