<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\UserProfile;
use App\Http\Controllers\Api\ApiTransformer;


class UserProfileTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserProfile $profile)
	{
		$firstName = $profile->user->forgotten ? __('profile.account') : $profile->first_name;
		$lastName = $profile->user->forgotten ? __('profile.deleted') : $profile->last_name;
		$displayName = $profile->user->forgotten ? __('profile.account-deleted') : $profile->display_name;

		$data = [
			'id'                => $profile->id,
			'user_id'           => $profile->user_id,
			'first_name'        => $firstName,
			'last_name'         => $lastName,
			'public_email'      => $profile->public_email,
			'public_phone'      => $profile->public_phone,
			'username'          => $profile->username,
			'display_name'      => $displayName,
			'full_name'         => $profile->full_name,
			'avatar'            => $profile->avatar_url,
			'city'              => $profile->city,
			'university'        => $profile->university,
			'specialization'    => $profile->specialization,
			'help'              => $profile->help,
			'interests'         => $profile->interests,
			'about'             => $profile->about,
			'learning_location' => $profile->learning_location,
			'roles'             => $profile->user->roles->pluck('name')->toArray() ?? [],
			'forgotten'         => $profile->user->forgotten,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
