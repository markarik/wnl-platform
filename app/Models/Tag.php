<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tag extends Model {
	// TODO make sure this list is complete
	const PROTECTED_TAGGABLE_TYPES = [
		'App\\Models\\Lesson',
		'App\\Models\\Page',
	];

	protected $fillable = ['name', 'description', 'color'];

	protected $touches = ['questions'];

	public function questions() {
		return $this->morphedByMany('App\Models\QnaQuestion', 'taggable');
	}

	public function lessons() {
		return $this->morphedByMany('App\Models\Lesson', 'taggable');
	}

	public function hasRelations() {
		return DB::table('taggables')
			->select('tag_id')
			->where('tag_id', $this->id)
			->exists();
	}

	public function isInTaxonomy() {
		return DB::table('tags_taxonomy')
			->select('tag_id')
			->where('tag_id', $this->id)
			->exists();
	}

	/**
	 * SlidesFromCategory command uses hardcoded tag names
	 * @see SlidesFromCategory
	 *
	 * @return bool
	 */
	public function isCategoryTag() {
		return DB::table('categories')
			->select('id')
			->where('name', $this->name)
			->exists();
	}

	public function isProtectedTaggable() {
		return DB::table('taggables')
			->select('tag_id')
			->where('tag_id', $this->id)
			->whereIn('taggable_type', static::PROTECTED_TAGGABLE_TYPES)
			->exists();
	}

	public function isProtected() {
		return $this->isInTaxonomy() || $this->isCategoryTag() || $this->isProtectedTaggable();
	}
}
