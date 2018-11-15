<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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


	public function userFlashcardNotes() {
		return $this->hasMany('\App\Models\UserFlashcardNote')->where('user_id', '=', Auth::user()->id);
	}
}
