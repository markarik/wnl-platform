<?php


namespace App\Events;


trait TransformsEventActor
{
	public function transformActor($actor)
	{
		return [
			'id' => $actor->id,
			'first_name' => $actor->profile->first_name,
			'last_name' => $actor->profile->last_name,
			'full_name' => $actor->profile->full_name,
			'avatar' => $actor->profile->avatar_url,
			'roles' => $actor->roles_names,
		];
	}
}
