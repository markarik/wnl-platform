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
			->where('tag_id', $this->id);
	}

	public function isInTaxonomy() {
		return DB::table('tags_taxonomy')
			->select('tag_id')
			->where('tag_id', $this->id)
			->exists();
	}

	/**
	 * Our structure depends on some tags and we shouldn't delete them
	 * TODO refactor the structure and remove this method
	 */
	public function isProtected() {
		$isProtectedTaggable = DB::table('taggables')
			->select('tag_id')
			->where('tag_id', $this->id)
			->whereIn('taggable_type', static::PROTECTED_TAGGABLE_TYPES)
			->exists();

		// @see Commands/SlidesFromCategory
		$isCategoryTag = DB::table('categories')
			->select('id')
			->where('name', $this->name)
			->exists();

		return $isProtectedTaggable || $isCategoryTag;
	}
}
