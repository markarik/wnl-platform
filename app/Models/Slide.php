<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
	use Cached;

    protected $fillable = ['content', 'is_functional'];

	protected $casts = [
		'is_functional'	=> 'bool',
	];

	public function categories() {
		return $this->morphedByMany('\App\Models\Category', 'presentable');
	}

	public function screens() {
		return $this->morphedByMany('\App\Models\Screen', 'presentable');
	}

	public function sections() {
		return $this->morphedByMany('\App\Models\Section', 'presentable');
	}

	public function comments()
	{
		return $this->morphMany('\App\Models\Comment', 'commentable');
	}

	public function reactions()
	{
		return $this->morphToMany('App\Models\Reaction', 'reactable');
	}
}
