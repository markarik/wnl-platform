<?php

namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\UserFlashcardNote;


class UserFlashcardNoteTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserFlashcardNote $userFlashcardNote)
	{
		$data = [
			'id' => $userFlashcardNote->id,
			'flashcard_id' => $userFlashcardNote->flashcard_id,
			'user_id' => $userFlashcardNote->user_id,
			'note' => $userFlashcardNote->note,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
