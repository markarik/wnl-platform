<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Slide;
use App\Http\Controllers\Api\ApiTransformer;

class SlideTransformer extends ApiTransformer
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
