<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

class Taxonomy extends Model
{
	use Searchable;

	protected $fillable = ['name', 'description'];

	public function tagsTaxonomy() {
		return $this->hasMany('App\Models\TagsTaxonomy');
	}

	public function rootTagsFromTaxonomy() {
		return $this->tagsTaxonomy()
			->with('tag')
			->where('parent_tag_id', 0)
			->get();
	}
}
