<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Group;
use App\Http\Controllers\Api\ApiTransformer;

class GroupTransformer extends ApiTransformer
{
	protected $availableIncludes = ['lessons'];

	protected $editionId;

	public function __construct($editionId = null)
	{
		$this->editionId = $editionId;
	}

	public function transform(Group $group)
	{
		return [
			'id'            => $group->id,
			'name'          => $group->name,
			'editions'      => $group->course_id
		];
	}

	public function includeLessons(Group $group)
	{
		$lessons = $group->lessons->sortBy('order_number');
		$meta = [
			'editionId' => $this->editionId,
			'groupId'   => $group->id,
		];

		return $this->collection($lessons, new LessonTransformer($meta), 'lessons');
	}
}
