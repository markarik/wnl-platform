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
			'id'     => $screen->id,
			'name'   => $screen->name,
			'lesson' => $screen->lesson_id,
		];
	}

	public function includeSections(Screen $screen)
	{
		$sections = $screen->sections;

		return $this->collection($sections, new SectionsTransformer, 'section');
	}
}