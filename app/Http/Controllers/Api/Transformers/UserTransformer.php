<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\User;
use App\Http\Controllers\Api\ApiTransformer;


class UserTransformer extends ApiTransformer
{
	protected $parent;

	protected $availableIncludes = ['roles'];

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
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeRoles(User $profile)
	{
		$roles = $profile->roles;

		return $this->collection(
			$roles,
			new RoleTransformer(['users' => $profile->id]),
			'roles'
		);
	}
}
