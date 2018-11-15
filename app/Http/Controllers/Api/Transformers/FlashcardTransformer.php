<?php

namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Flashcard;


class FlashcardTransformer extends ApiTransformer
{
	protected $parent;
	protected $availableIncludes = ['user_flashcard_notes'];

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Flashcard $flashcard)
	{
		$data = [
			'id' => $flashcard->id,
			'content' => $flashcard->content,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeUserFlashcardNotes(Flashcard $flashcard)
	{
		return $this->collection(
			$flashcard->userFlashcardNotes,
			new UserFlashcardNoteTransformer(['flashcards' => $flashcard->id]),
			'user_flashcard_notes'
		);
	}
}
