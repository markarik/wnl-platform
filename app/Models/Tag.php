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

	public function hasRelations() {
		$relationsCount = DB::table('taggables')
			->select(DB::raw('count(1) as count'))
			->join('tags', 'tags.id', '=', 'taggables.tag_id')
			->groupBy('tags.id')
			->where('tag_id', $this->id)
			->count();

		return $relationsCount > 0;
	}

	public function isInTaxonomy() {
		$tagsTaxonomyCount = DB::table('tags_taxonomy')
			->select(DB::raw('count(1) as count'))
			->join('tags', function ($join) {
				$join->on('tags.id', '=', 'tags_taxonomy.tag_id');
				$join->orOn('tags.id', '=', 'tags_taxonomy.parent_tag_id');
			})
			->groupBy('tags.id')
			->where('tag_id', $this->id)
			->count();

		return $tagsTaxonomyCount > 0;
	}
}
