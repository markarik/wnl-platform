<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class UserFlashcardNote extends Model
{
	protected $fillable = ['user_id', 'flashcard_id', 'note'];

	public function user()
	{
		return $this->belongsTo('\App\Models\User');
	}

	public function flashcard()
	{
		return $this->belongsTo('\App\Models\Flashcard');
	}
}
