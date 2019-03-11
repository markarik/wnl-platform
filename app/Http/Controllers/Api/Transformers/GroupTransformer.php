<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Group;
use App\Http\Controllers\Api\ApiTransformer;

class GroupTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Group $group)
	{
		$data = [
			'id' => $group->id,
			'name' => $group->name,
			'courses' => $group->course_id
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
