<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tag extends Model {
	protected $fillable = ['name', 'description', 'color'];

	protected $touches = ['questions'];

	public function questions() {
		return $this->morphedByMany('App\Models\QnaQuestion', 'taggable');
	}

	public function lessons() {
		return $this->morphedByMany('App\Models\Lesson', 'taggable');
	}

	public function isInTaxonomy() {
		return DB::table('tags_taxonomy')
			->select('tag_id')
			->where('tag_id', $this->id)
			->exists();
	}

	public function isCategoryTag() {
		return DB::table('categories')
			->select('id')
			->where('name', $this->name)
			->exists();
	}

	public function hasTaggable() {
		return DB::table('taggables')
			->select('tag_id')
			->where('tag_id', $this->id)
			->exists();
	}

	public function hasProtectedTaggable() {
		return DB::table('taggables')
			->select('tag_id')
			->where('tag_id', $this->id)
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
