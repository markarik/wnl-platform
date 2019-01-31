<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

/**
 * Class TaxonomyTerm
 * @package App\Models
 *
 * NodeTrait Docs: https://github.com/lazychaser/laravel-nestedset
 */
class TaxonomyTerm extends Model
{
	use NodeTrait;

	protected $fillable = ['description', 'tag_id', 'taxonomy_id'];

	public function taxonomy() {
		return $this->belongsTo('App\Models\Taxonomy');
	}

	public function tag() {
		return $this->belongsTo('App\Models\Tag');
	}

	public function annotations() {
		return $this->morphedByMany('App\Models\Annotation', 'taxonomy_termable');
	}

	public function flashcards() {
		return $this->morphedByMany('App\Models\Flashcard', 'taxonomy_termable');
	}

	public function quizQuestions() {
		return $this->morphedByMany('App\Models\QuizQuestion', 'taxonomy_termable');
	}

	public function slides() {
		return $this->morphedByMany('App\Models\Slide', 'taxonomy_termable');
	}
}
