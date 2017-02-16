<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
	protected $fillable = ['name', 'group_id'];

	public function snippets() {
		return $this->belongsToMany('\App\Models\Snippet', 'screens');
	}
}
