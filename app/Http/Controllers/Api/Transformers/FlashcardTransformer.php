<?php

namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Flashcard;


class FlashcardTransformer extends ApiTransformer
{
	protected $availableIncludes = ['tags', 'taxonomy_terms', 'user_flashcard_notes'];
	protected $parent;

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

	public function includeTags(Flashcard $flashcard)
	{
		$tags = $flashcard->tags;

		return $this->collection($tags, new TagTransformer(['flashcards' => $flashcard->id]), 'tags');
	}

	public function includeTaxonomyTerms(Flashcard $flashcard)
	{
		return $this->collection(
			$flashcard->taxonomyTerms,
			new TaxonomyTermTransformer(['flashcards' => $flashcard->id]),
			'taxonomy_terms'
		);
	}
}
