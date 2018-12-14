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
		$count = DB::table('taggables')
			->select(DB::raw('count(1) as count'))
			->where('tag_id', $this->id)
			->groupBy('tag_id')
			->count();

		return $count > 0;
	}

	public function isInTaxonomy() {
		$count = DB::table('tags_taxonomy')
			->select(DB::raw('count(1) as count'))
			->where('tag_id', $this->id)
			->groupBy('tag_id')
			->count();

		return $count > 0;
	}

	/**
	 * Our structure depends on some tags and we shouldn't delete them
	 * TODO refactor the structure and remove this method
	 */
	public function isProtected() {
		$count = DB::table('taggables')
			->select(DB::raw('count(1) as count'))
			->where('tag_id', $this->id)
			->whereIn('taggable_type', static::PROTECTED_TAGGABLE_TYPES)
			->groupBy('tag_id')
			->count();

		return $count > 0;
	}
}
