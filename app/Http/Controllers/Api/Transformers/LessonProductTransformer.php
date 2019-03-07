<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\LessonProduct;


class LessonProductTransformer extends ApiTransformer
{
	protected $availableIncludes = [];

	public function transform(LessonProduct $lessonProduct)
	{
		$data = [
			'id' => $lessonProduct->id,
			'product_id' => $lessonProduct->product_id,
			'lesson_id' => $lessonProduct->lesson_id,
			'start_date' => $lessonProduct->start_date->timestamp
		];

		return $data;
	}
}
