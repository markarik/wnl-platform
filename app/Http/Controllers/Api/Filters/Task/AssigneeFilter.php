<?php namespace App\Http\Controllers\Api\Filters\Task;

use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\Role;
use App\Models\Profile;
use App\Http\Controllers\Api\Transformers\ProfileTransformer;
use League\Fractal\Resource\Collection;
use App\Http\Controllers\Api\Serializer\ApiJsonSerializer;
use League\Fractal\Manager;


class AssigneeFilter extends ApiFilter
{
	protected $expected = ['user_id'];

	public function handle($builder)
	{
		$userId = $this->params['user_id'];
		return $builder->where('assignee_id', $userId);
	}

	public function count($builder)
	{
		$profiles = Profile::whereHas('roles', function ($query) {
			$query->whereIn('roles.name', [Role::ROLE_ADMIN, Role::ROLE_MODERATOR]);
		})->get();

		$resource = new Collection($profiles, new ProfileTransformer, 'user_profiles');

		$fractal = new Manager();
		$fractal->setSerializer(new ApiJsonSerializer());

		$data = $fractal->createData($resource)->toArray();

		return $data;
	}
}
