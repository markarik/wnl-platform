<?php

namespace App\Models;

use App\Models\Contracts\WithTags;
use Illuminate\Database\Eloquent\Model;

class Page extends Model implements WithTags
{
	protected $fillable = ['content', 'name', 'slug'];

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}

	public function discussion()
	{
		return $this->belongsTo('\App\Models\Discussion');
	}
}
