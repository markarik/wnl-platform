<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Slide;
use League\Fractal\TransformerAbstract;

class SlideTransformer extends TransformerAbstract
{
	public function transform(Slide $slide)
	{
		$data = [
			'content'       => $slide->content,
			'is_functional' => $slide->is_functional,
		];

		return $data;
	}
}
