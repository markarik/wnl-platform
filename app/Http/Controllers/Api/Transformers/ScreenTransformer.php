<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Screen;
use League\Fractal\TransformerAbstract;

class ScreenTransformer extends TransformerAbstract
{
	protected $availableIncludes = ['sections'];

	public function transform(Screen $screen)
	{
		return [
			'id'           => $screen->id,
			'name'         => $screen->name,
			'content'      => $screen->content,
			'type'         => $screen->type,
			'meta'         => $screen->meta,
			'order_number' => $screen->order_number,
			'lessons'      => $screen->lesson_id,
			'groups'       => $screen->lesson->group->id,
			'editions'     => $screen->lesson->group->course->id,

		];
	}

	public function includeSections(Screen $screen)
	{
		$sections = $screen->sections;

		return $this->collection($sections, new SectionsTransformer, 'sections');
	}
}
