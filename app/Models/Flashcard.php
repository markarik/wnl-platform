<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flashcard extends Model
{
	protected $fillable = ['content'];

	public function flashcardsSets()
	{
		return	$this->belongsToMany(
			'\App\Models\FlashcardsSet',
			'flashcards_set_flashcard',
			'flashcard_id',
			'flashcard_set_id'
		)->withPivot('order_number');
	}
}
