<?php

namespace App\Models;

use App\Scopes\OrderByOrderNumberScope;
use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

class FlashcardsSet extends Model
{
	use Searchable;

	protected $fillable = ['description', 'mind_maps_text', 'name', 'lesson_id'];

	protected static function boot() {
		parent::boot();
		static::addGlobalScope(new OrderByOrderNumberScope());
	}

	public function flashcards()
	{
		return	$this->belongsToMany(
			'\App\Models\Flashcard',
			'flashcards_set_flashcard',
			'flashcard_set_id',
			'flashcard_id'
		)->withPivot('order_number');
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
