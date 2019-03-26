<?php


namespace App\Http\Controllers\Api\Transformers;

use App\Models\UserProfile;
use App\Http\Controllers\Api\ApiTransformer;


class UserProfileTransformer extends ApiTransformer
{
	protected $parent;

	protected $availableIncludes = ['roles', 'has_prolonged_course'];

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserProfile $profile)
	{
		$firstName = is_null($profile->deleted_at) ? $profile->first_name : 'Konto';
		$lastName = is_null($profile->deleted_at) ? $profile->last_name : 'usunięte';
		$displayName = is_null($profile->deleted_at) ? $profile->display_name : 'Konto usunięte';

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
			'deleted_at'        => $profile->deleted_at,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeRoles(UserProfile $profile)
	{
		$roles = $profile->roles;

		return $this->collection(
			$roles,
			new RoleTransformer(['user_profiles' => $profile->id]),
			'roles'
		);
	}

	public function includeHasProlongedCourse(UserProfile $profile)
	{
		return $this->item(
			$profile,
			new UserHasProlongedCourseTransformer(['user_profiles' => $profile->id]),
			'has_prolonged_course'
		);
	}
}
