<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model
{
	protected $fillable = ['name'];

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
