<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\User;
use App\Http\Controllers\Api\ApiTransformer;


class UserTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(User $user)
	{
		$data = [
			'id'         => $user->id,
			'first_name' => $user->first_name,
			'last_name'  => $user->last_name,
			'full_name'  => $user->full_name,
			'avatar'     => $user->profile->avatar_url,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
