<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Group;
use App\Http\Controllers\Api\ApiTransformer;

class GroupTransformer extends ApiTransformer
{
	protected $availableIncludes = ['lessons'];

	protected $editionId;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Group $group)
	{
		$data = [
			'id'            => $group->id,
			'name'          => $group->name,
			'editions'      => $group->course_id
		];


		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeLessons(Group $group)
	{
		$lessons = $group->lessons->sortBy('order_number');
		$meta = collect([
			'editionId' => $this->parent['editionId'] ?? null,
			'groupId'   => $group->id,
		]);

		return $this->collection($lessons, new LessonTransformer($meta), 'lessons');
	}
}
