<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

class Tag extends Model
{
	use Searchable;

	protected $fillable = ['name', 'description', 'color'];

	protected $touches = ['questions'];

	public function questions()
	{
		return $this->morphedByMany('App\Models\QnaQuestion', 'taggable');
	}

	public function lessons()
	{
		return $this->morphedByMany('App\Models\Lesson', 'taggable');
	}

	public function taggables()
	{
		return $this->hasMany('App\Models\Taggable');
	}

	public function taxonomies()
	{
		return $this->belongsToMany('App\Models\Taxonomy', 'tags_taxonomy');
	}

	public function delete()
	{
		\DB::transaction(function () {
			$this->taggables()->delete();
			parent::delete();
		});
	}

	public function isInTaxonomy()
	{
		return $this->taxonomies()->exists();
	}

	public function isCategoryTag()
	{
		return Category::where('name', $this->name)->exists();
	}

	public function isRenameAllowed()
	{
		// SlidesFromCategory command uses hardcoded tag names
		return !$this->isCategoryTag();
	}

	public function isDeleteAllowed()
	{
		return !$this->isInTaxonomy() && !$this->isCategoryTag();
	}
}
