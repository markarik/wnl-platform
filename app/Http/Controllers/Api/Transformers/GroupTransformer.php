<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Group;
use League\Fractal\TransformerAbstract;

class GroupTransformer extends TransformerAbstract
{
	protected $availableIncludes = ['lessons'];

	public function transform(Group $group)
	{
		return [
			'id'     => $group->id,
			'name'   => $group->name,
			'edition' => $group->course_id,
		];
	}

	public function includeLessons(Group $group)
	{
		$lessons = $group->lessons;

		return $this->collection($lessons, new LessonTransformer, 'lesson');
	}
}
