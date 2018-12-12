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
		$data = [
			'id'          => $subsection->id,
			'name'        => $subsection->name,
			'lessons'     => $this->parent->get('lessonId') ?? $subsection->section->screen->lesson_id,
			'groups'      => $this->parent->get('groupId') ?? $subsection->section->screen->lesson->group->id,
			'screens'     => $subsection->section->screen_id,
			'sections'    => $subsection->section->id,
			'slide'       => $subsection->first_slide + 1,
			'slidesCount' => $subsection->slides_count,
		];

		return $data;
	}

}
