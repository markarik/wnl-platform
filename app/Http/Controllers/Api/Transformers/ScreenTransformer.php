<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Screen;
use App\Http\Controllers\Api\ApiTransformer;

class ScreenTransformer extends ApiTransformer
{
	protected $availableIncludes = ['sections'];
	protected $parent;

	public function __construct($parentData = [])
	{
		$this->parent = collect($parentData);
	}

	public function transform(Screen $screen)
	{

		return [
			'id'           => $screen->id,
			'name'         => $screen->name,
			'content'      => $screen->content,
			'type'         => $screen->type,
			'meta'         => $screen->meta,
			'order_number' => $screen->order_number,
			'lessons'      => $this->parent->get('lessonId') ?? $screen->lesson_id,
			'groups'       => $this->parent->get('groupId') ?? $screen->lesson->group->id,
			'editions'     => $this->parent->get('editionId'),
		];
	}

	public function includeSections(Screen $screen)
	{
		$sections = $screen->sections;

		$meta = collect([
			'screenId' => $screen->id,
		]);
		$meta = $meta->merge($this->parent);

		return $this->collection($sections, new SectionsTransformer($meta), 'sections');
	}
}
