<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Lesson;
use App\Models\Section;
use App\Models\Screen;
use League\Fractal\TransformerAbstract;

class SectionsTransformer extends TransformerAbstract
{
	protected $availableIncludes = [];

	public function transform(Section $section)
	{
		return [
			'id'     => $section->id,
			'name'   => $section->name,
			'screen' => $section->screen_id,
		];
	}

}