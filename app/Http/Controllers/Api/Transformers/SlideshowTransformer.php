<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Slideshow;
use App\Http\Controllers\Api\ApiTransformer;

class SlideshowTransformer extends ApiTransformer
{
	public function transform(Slideshow $slideshow)
	{
		$data = [
			'id'             => $slideshow->id,
			'background_url' => $slideshow->background_url
		];

		return $data;
	}
}
