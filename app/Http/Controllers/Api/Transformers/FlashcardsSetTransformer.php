<?php

namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\FlashcardsSet;


class FlashcardsSetTransformer extends ApiTransformer
{
	protected $availableIncludes = ['flashcards'];

	public function transform(FlashcardsSet $set)
	{
		$data = [
			'id' => $set->id,
			'description' => $set->description,
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
}
