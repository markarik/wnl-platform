<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    protected $fillable = ['content', 'is_functional'];

	protected $casts = [
		'is_functional'	=> 'bool',
	];

	public function categories() {
		return $this->morphedByMany('\App\Models\Category', 'presentable');
	}

	public function snippets() {
		return $this->morphedByMany('\App\Models\Snippet', 'presentable');
	}
}
