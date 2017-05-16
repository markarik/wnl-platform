<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\UserProfile;
use League\Fractal\TransformerAbstract;


class UserProfileTransformer extends TransformerAbstract
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserProfile $profile)
	{
		$data = [
			'id'           => $profile->id,
			'first_name'   => $profile->first_name,
			'last_name'    => $profile->last_name,
			'public_email' => $profile->public_email,
			'public_phone' => $profile->public_phone,
			'username'     => $profile->username,
			'full_name'    => $profile->full_name,
			'avatar'       => $profile->avatar_url,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
