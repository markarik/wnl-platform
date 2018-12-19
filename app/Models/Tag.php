<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use ScoutEngines\Elasticsearch\Searchable;

class Tag extends Model
{
	use Searchable;

	protected $fillable = ['name', 'description', 'color'];

	protected $touches = ['questions'];

	public function questions() {
		return $this->morphedByMany('App\Models\QnaQuestion', 'taggable');
	}

	public function lessons() {
		return $this->morphedByMany('App\Models\Lesson', 'taggable');
	}

	public function taggables()
	{
		return $this->hasMany('App\Models\Taggable');
	}

	public function delete() {
		\DB::transaction(function () {
			$this->taggables()->delete();
			parent::delete();
		});
	}

	public function isInTaxonomy() {
		return DB::table('tags_taxonomy')
			->select('tag_id')
			->where('tag_id', $this->id)
			->exists();
	}

	public function isCategoryTag() {
		return Category::where('name', $this->name)->exists();
	}

	public function hasProtectedTaggable() {
		return $this->taggables()
			->whereIn('taggable_type', Taggable::PROTECTED_TAGGABLE_TYPES)
			->exists();
	}

	public function isRenameAllowed() {
		// SlidesFromCategory command uses hardcoded tag names
		return !$this->isCategoryTag();
	}

	public function isDeleteAllowed() {
		return !$this->isInTaxonomy() && !$this->isCategoryTag() && !$this->hasProtectedTaggable();
	}
}
