<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Presentable;
use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Slide;


class PresentableTransformer extends ApiTransformer
{
	public function transform(Presentable $presentable)
	{
		$data = [
			'id'               => $presentable->id,
			'slide_id'         => $presentable->slide_id,
			'presentable_id'   => $presentable->presentable_id,
			'presentable_type' => $presentable->presentable_type,
			'order_number'     => $presentable->order_number,
			'is_functional'    => $presentable->is_functional,
		];

		if (self::shouldInclude('reactions')) {
			$slide = Slide::find($presentable->slide_id);
			$data = array_merge($data, ReactionsCountTransformer::transform($slide));
		}

		return $data;
	}
}
