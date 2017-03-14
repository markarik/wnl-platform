<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Group;
use League\Fractal\TransformerAbstract;

class GroupTransformer extends TransformerAbstract
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
			'id'      => $group->id,
			'name'    => $group->name,
			'editions' => $group->course_id,
		];
	}

	public function includeLessons(Group $group)
	{
		$lessons = $group->lessons()->with(['availability'])->get();

		return $this->collection($lessons, new LessonTransformer($this->editionId), 'lessons');
	}
}
