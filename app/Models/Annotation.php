<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

class Annotation extends Model
{
	use Searchable;

	protected $fillable = ['title', 'description'];

	public function keywords()
	{
		return $this->hasMany('App\Models\Keyword');
	}

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}

	public function toSearchableArray()
	{
		$tags = $this->tags;

		$data = [
			'id'         => $this->id,
			'title'      => $this->title,
			'description' => $this->description,
			'tags'       => $tags->map(function ($tag) {
				return ['id' => $tag->id, 'name' => $tag->name];
			}),
		];

		return $data;
	}
}
