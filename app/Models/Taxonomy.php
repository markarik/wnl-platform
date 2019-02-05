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

	public function taxonomyTerms() {
		return $this->hasMany('App\Models\TaxonomyTerm');
	}

	public function rootTagsFromTaxonomy() {
		return $this->tagsTaxonomy()
			->with('tag')
			->where('parent_tag_id', 0)
			->get();
	}

	public function delete() {
		\DB::transaction(function() {
			$this->taxonomyTerms()->delete();
			parent::delete();
		});
	}
}
