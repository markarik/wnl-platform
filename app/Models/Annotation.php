<?php

namespace App\Models;

use App\Models\Concerns\WithTags;
use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

class Annotation extends Model implements WithTags
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

	public function taxonomyTerms()
	{
		return $this->morphToMany('App\Models\TaxonomyTerm', 'taxonomy_termable');
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
