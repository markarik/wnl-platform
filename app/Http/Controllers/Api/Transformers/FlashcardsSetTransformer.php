<?php

namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\FlashcardsSet;


class FlashcardsSetTransformer extends ApiTransformer
{
	protected $availableIncludes = ['flashcards', 'lesson'];

	public function transform(FlashcardsSet $set)
	{
		$data = [
			'id' => $set->id,
			'description' => $set->description,
			'mind_maps_text' => $set->mind_maps_text
		];

		return $data;
	}

	public function includeFlashcards(FlashcardsSet $set)
	{
		return $this->collection(
			$set->flashcards()->orderBy('pivot_order_number')->get(),
			new FlashcardTransformer(['flashcards_sets' => $set->id]),
			'flashcards'
		);
	}

	public function includeLesson(FlashcardsSet $set)
	{
		return $this->item(
			$set->lesson,
			new LessonTransformer(['flashcards_sets' => $set->id]),
			'lesson'
		);
	}
}
