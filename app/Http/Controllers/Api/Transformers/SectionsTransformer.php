<?php


namespace App\Http\Controllers\Api\Transformers;

use DB;
use App\Models\Section;
use App\Http\Controllers\Api\ApiTransformer;

class SectionsTransformer extends ApiTransformer
{
	protected $availableIncludes = [];
	protected $parent;

	public function __construct($parentData = [])
	{
		$this->parent = collect($parentData);
	}

	public function transform(Section $section)
	{
		$orderNumber = DB::table('presentables')
			->select('order_number')
			->where('presentable_type', 'App\Models\Section')
			->where('presentable_id', $section->id)
			->limit(1)
			->get(['order_number'])
			->first()
			->order_number;

		$data = [
			'id'       => $section->id,
			'name'     => $section->name,
			'lessons'  => $this->parent->get('lessonId') ?? $section->screen->lesson_id,
			'groups'   => $this->parent->get('groupId') ?? $section->screen->lesson->group->id,
			'editions' => $this->parent->get('editionId'),
			'screens'  => $section->screen_id,
			'slide'    => $orderNumber + 1,
		];

		return $data;
	}

}
