<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Presentable;
use League\Fractal\TransformerAbstract;


class PresentableTransformer extends TransformerAbstract
{
	public function transform(Presentable $presentable)
	{
		$data = [
			'id'               => $presentable->id,
			'slide_id'         => $presentable->slide_id,
			'presentable_id'   => $presentable->presentable_id,
			'presentable_type' => $presentable->presentable_type,
			'order_number'     => $presentable->order_number,
		];

		return $data;
	}
}
