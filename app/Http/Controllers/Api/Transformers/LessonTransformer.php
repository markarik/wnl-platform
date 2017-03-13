<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Lesson;
use League\Fractal\TransformerAbstract;


class LessonTransformer extends TransformerAbstract
{
	protected $availableIncludes = ['screens'];

	protected $editionId;

	public function __construct($editionId = null)
	{
		$this->editionId = $editionId;
	}

	public function transform(Lesson $lesson)
	{
		$data = [
			'id'    => $lesson->id,
			'name'  => $lesson->name,
			'group' => $lesson->group_id,
		];

		if ($this->editionId !== null) {
			$data['isAvailable'] = $lesson->isAvailable($this->editionId);
		}

		return $data;
	}

	public function includeScreens(Lesson $lesson)
	{
		$screens = $lesson->screens;

		return $this->collection($screens, new ScreenTransformer, 'screen');
	}
}