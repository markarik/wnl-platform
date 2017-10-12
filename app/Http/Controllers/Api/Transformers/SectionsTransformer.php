<?php


namespace App\Http\Controllers\Api\Transformers;

use DB;
use App\Models\Section;
use App\Http\Controllers\Api\ApiTransformer;

class SectionsTransformer extends ApiTransformer
{
	protected $availableIncludes = ['subsections'];
	protected $parent;

	public function __construct($parentData = [])
	{
		$this->parent = collect($parentData);
	}

	public function transform(Section $section)
	{
		$sectionSlides = DB::table('presentables')
			->select('order_number')
			->where('presentable_type', 'App\Models\Section')
			->where('presentable_id', $section->id)
			->get(['order_number']);

		$firstSlideNumber = $sectionSlides->first()->order_number;
		$slidesCount = $sectionSlides->count();

		$data = [
			'id'          => $section->id,
			'name'        => $section->name,
			'lessons'     => $this->parent->get('lessonId') ?? $section->screen->lesson_id,
			'groups'      => $this->parent->get('groupId') ?? $section->screen->lesson->group->id,
			'editions'    => $this->parent->get('editionId'),
			'screens'     => $section->screen_id,
			'slide'       => $firstSlideNumber + 1,
			'slidesCount' => $slidesCount,
		];

		return $data;
	}

	public function includeSubsections(Section $section) {
		$subsections = $section->subsections;

		$meta = collect([
			'sectionId' => $section->id,
		]);
		$meta = $meta->merge($this->parent);

		return $this->collection($subsections, new SubsectionsTransformer($meta), 'subsections');
	}

}
