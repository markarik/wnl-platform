<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashcardsSet extends Model
{
	protected $fillable = ['description'];

	public function flashcards()
	{
		return	$this->belongsToMany(
			'\App\Models\Flashcard',
			'flashcards_sets_flashcards',
			'flashcard_set_id',
			'flashcard_id'
		)->withPivot('order_number');
	}
}
