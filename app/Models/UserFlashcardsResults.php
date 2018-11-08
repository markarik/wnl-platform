<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class UserFlashcardsResults extends Model
{
	protected $fillable = ['user_id', 'flashcard_id', 'answer'];

	protected $table = 'user_flashcards_results';

	public function user()
	{
		return $this->belongsTo('\App\Models\User');
	}

	public function flashcard()
	{
		return $this->belongsTo('\App\Models\Flashcard');
	}

	public function context()
	{
		return $this->morphTo();
	}
}
