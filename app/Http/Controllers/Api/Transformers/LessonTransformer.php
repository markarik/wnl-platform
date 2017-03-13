<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Lesson;
use League\Fractal\TransformerAbstract;

class LessonTransformer extends TransformerAbstract
{
	protected $availableIncludes = ['screens'];

	public function transform(Lesson $lesson)
	{
		return [
			'id'    => $lesson->id,
			'name'  => $lesson->name,
			'group' => $lesson->group_id,
		];
	}

	public function includeScreens(Lesson $lesson)
	{
		$screens = $lesson->screens;

		return $this->collection($screens, new ScreenTransformer, 'screen');
	}
}