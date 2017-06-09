<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Presentable;
use App\Http\Controllers\Api\ApiTransformer;


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

		return $data;
	}
}
