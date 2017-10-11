<?php


namespace App\Http\Controllers\Api\Transformers;

use DB;
use App\Models\Section;
use App\Http\Controllers\Api\ApiTransformer;

class SubsectionsTransformer extends ApiTransformer
{
	protected $availableIncludes = [];
	protected $parent;

	public function __construct($parentData = [])
	{
		$this->parent = collect($parentData);
	}

	public function transform(Section $section)
	{
		$subsectionSlides = DB::table('presentables')
			->select('order_number')
			->where('presentable_type', 'App\Models\Subsection')
			->where('presentable_id', $section->id)
			->get(['order_number']);

		$firstSlideNumber = $subsectionSlides->orderBy('order_number', 'asc')->first()->order_number;
		$slidesCount = $subsectionSlides->count();

		$data = [
			'id'          => $subsection->id,
			'name'        => $subsection->name,
			'lessons'     => $this->parent->get('lessonId') ?? $subsection->section->screen->lesson_id,
			'groups'      => $this->parent->get('groupId') ?? $subsection->section->screen->lesson->group->id,
			'editions'    => $this->parent->get('editionId'),
			'screens'     => $subsection->section->screen_id,
			'section'     => $subsection->section->screen_id,
			'slide'       => $firstSlideNumber + 1,
			'slidesCount' => $slidesCount,
		];

		return $data;
	}

}
