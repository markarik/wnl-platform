<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Slideshow;
use League\Fractal\TransformerAbstract;

class SlideshowTransformer extends TransformerAbstract
{
	public function transform(Slideshow $slideshow)
	{
		$data = [
			'id'             => $slideshow->id,
			'background_url' => $slideshow->background_url,
		];

		return $data;
	}
}
