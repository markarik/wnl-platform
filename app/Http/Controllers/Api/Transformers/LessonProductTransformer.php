<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\LessonProduct;


class LessonProductTransformer extends ApiTransformer
{
	protected $availableIncludes = ['lessons'];

	public function transform(LessonProduct $lessonProduct)
	{
		$data = [
			'id'                    => $lessonProduct->id,
			'user_id'               => $lessonProduct->product_id,
			'lesson_id'             => $lessonProduct->lesson_id,
			'start_date'            => $lessonProduct->start_date,
		];

		return $data;
	}

	public function includeLessons(LessonProduct $lessonProduct)
	{
		return $this->item(
			$lessonProduct->lesson,
			new LessonTransformer(['lesson_product' => $lessonProduct->id]),
			'lesson'
		);
	}
}
