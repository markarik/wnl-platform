<?php


namespace App\Http\Controllers\Api\Transformers;

use App\Models\Profile;
use App\Http\Controllers\Api\ApiTransformer;


class ProfileTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Profile $profile)
	{
		$data = [
			'id' => $profile->id,
			'user_id' => $profile->user_id,
			'first_name' => $profile->first_name,
			'last_name' => $profile->last_name,
			'public_email' => $profile->public_email,
			'public_phone' => $profile->public_phone,
			'username' => $profile->username,
			'full_name' => $profile->full_name,
			'avatar' => $profile->avatar_url,
			'city' => $profile->city,
			'university' => $profile->university,
			'specialization' => $profile->specialization,
			'help' => $profile->help,
			'interests' => $profile->interests,
			'about' => $profile->about,
			'learning_location' => $profile->learning_location,
			'deleted_at' => $profile->deleted_at,
			'roles' => $profile->roles_names,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
