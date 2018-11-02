<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashcardsSet extends Model
{
	protected $fillable = ['description', 'mind_maps_text', 'name'];

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
}