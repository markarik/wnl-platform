<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

class FlashcardsSet extends Model
{
	use Searchable;

	protected $fillable = ['description', 'mind_maps_text', 'name', 'lesson_id'];

	public function flashcards()
	{
		return	$this->belongsToMany(
			'\App\Models\Flashcard',
			'flashcards_set_flashcard',
			'flashcard_set_id',
			'flashcard_id'
		)
			->withPivot('order_number')
			->orderBy('pivot_order_number');
	}

	public function lesson()
	{
		return	$this->belongsTo('\App\Models\Lesson');
	}

	public function syncFlashcards($flashcardIds) {
		$orderNumber = 0;
		$flashcardsSync = [];
		foreach($flashcardIds as $flashcardId) {
			$flashcardsSync[$flashcardId] = ['order_number' => $orderNumber++];
		}

		$this->flashcards()->sync($flashcardsSync);
	}
}
