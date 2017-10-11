<?php


namespace App\Http\Controllers\Api\Transformers;

use DB;
use App\Models\Subsection;
use App\Http\Controllers\Api\ApiTransformer;

class SubsectionsTransformer extends ApiTransformer
{
	protected $availableIncludes = [];
	protected $parent;

	public function __construct($parentData = [])
	{
		$this->parent = collect($parentData);
	}

	public function transform(Subsection $subsection)
	{
		$subsectionSlides = DB::table('presentables')
			->select('order_number')
			->where('presentable_type', 'App\Models\Subsection')
			->where('presentable_id', $subsection->id)
			->orderBy('order_number', 'asc')
			->get(['order_number']);

		$firstSlideNumber = $subsectionSlides->first()->order_number;
		$slidesCount = $subsectionSlides->count();

		$data = [
			'id'          => $subsection->id,
			'name'        => $subsection->name,
			'lessons'     => $this->parent->get('lessonId') ?? $subsection->section->screen->lesson_id,
			'groups'      => $this->parent->get('groupId') ?? $subsection->section->screen->lesson->group->id,
			'editions'    => $this->parent->get('editionId'),
			'screens'     => $subsection->section->screen_id,
			'sections'    => $subsection->section->id,
			'slide'       => $firstSlideNumber + 1,
			'slidesCount' => $slidesCount,
		];

		return $data;
	}

}
