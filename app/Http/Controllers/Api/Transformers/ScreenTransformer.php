<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Screen;

class ScreenTransformer extends ApiTransformer
{
	protected $availableIncludes = ['sections'];
	protected $parent;

	public function __construct($parentData = [])
	{
		$this->parent = $parentData;
	}

	public function transform(Screen $screen)
	{
		$data = [
			'id'           => $screen->id,
			'name'         => $screen->name,
			'type'         => $screen->type,
			'meta'         => $screen->meta,
			'order_number' => $screen->order_number,
			'tags'         => $screen->tags,
			'lessons'      => $this->parent['lessonId'] ?? $screen->lesson_id,
			'discussion_id' => $screen->discussion_id,
			'is_discussable' => $screen->is_discussable
		];

		if (!empty($screen->meta['slides_count'])) {
			$data['slides_count'] = $screen->meta['slides_count'];
		}

		if (!ApiController::shouldExclude('screens.content')) {
			$data['content'] = $screen->content;
		}

		return $data;
	}

	public function includeSections(Screen $screen)
	{
		$sections = $screen->sections;

		$meta = [
			'screenId' => $screen->id,
		];
		$meta = array_merge($meta, $this->parent);

		return $this->collection($sections, new SectionsTransformer($meta), 'sections');
	}
}
