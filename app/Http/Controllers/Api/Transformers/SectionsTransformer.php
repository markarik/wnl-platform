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
		$this->parent = $parentData;
	}

	public function transform(Section $section)
	{
		$data = [
			'id'          => $section->id,
			'name'        => $section->name,
			'order_number'=> $section->order_number,
			'lessons'     => $this->parent['lessonId'] ?? $section->screen->lesson_id,
			'groups'      => $this->parent['groupId'] ?? $section->screen->lesson->group->id,
			'editions'    => $this->parent['editionId'],
			'screens'     => $section->screen_id,
			'slide'       => $section->first_slide + 1,
			'slidesCount' => $section->slides_count,
		];

		return $data;
	}

	public function includeSubsections(Section $section) {
		$subsections = $section->subsections->sortBy('order_number');

		$meta = [
			'sectionId' => $section->id,
		];
		$meta = array_merge($meta, $this->parent);

		return $this->collection($subsections, new SubsectionsTransformer($meta), 'subsections');
	}

}
