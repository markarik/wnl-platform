<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Facades\Lib\SlideParser\Parser;
use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

class Slide extends Model
{
	use Cached, Searchable;

	protected $fillable = ['content', 'is_functional', 'snippet'];

	protected $casts = [
		'is_functional' => 'bool',
		'snippet'       => 'array',
	];

	public function categories()
	{
		return $this->morphedByMany('\App\Models\Category', 'presentable');
	}

	public function sections()
	{
		return $this->morphedByMany('\App\Models\Section', 'presentable');
	}

	public function subsections()
	{
		return $this->morphedByMany('\App\Models\Subsection', 'presentable');
	}

	public function slideshow()
	{
		return $this->morphedByMany('\App\Models\Slideshow', 'presentable');
	}

	public function comments()
	{
		return $this->morphMany('\App\Models\Comment', 'commentable');
	}

	public function reactions()
	{
		return $this->morphToMany('App\Models\Reaction', 'reactable');
	}

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}

	public function taxonomyTerms()
	{
		return $this->morphToMany('App\Models\TaxonomyTerm', 'taxonomy_termable');
	}

	public function quizQuestions()
	{
		return $this->belongsToMany('App\Models\QuizQuestion', 'slide_quiz_question');
	}

	public function setContentAttribute($value)
	{
		$this->snippet = $value;
		$this->attributes['content'] = $value;
	}

	public function setSnippetAttribute($value)
	{
		$this->attributes['snippet'] = json_encode(Parser::createSnippet($value));
	}

	public function toSearchableArray()
	{
		$model = $this->toArray();
		$model['context'] = [];

		if (!empty($this->sections) && !empty($this->sections->first())) {
			$section = $this->sections->first();
			$screen = $section->screen ?? null;
			$lesson = $screen->lesson ?? null;
			$group = $lesson->group ?? null;

			if (!$screen || !$lesson || !$group) {
				// Don't index slide if it doesn't have
				// a parent lesson or screen.
				return [];
			}
			$orderNumber = (int)Presentable::where([
				['presentable_type', '=', 'App\\Models\\Slideshow'],
				['presentable_id', '=', $screen->slideshow->id],
				['slide_id', '=', $this->id],
			])->first()->order_number;

			$model['context']['section']['id'] = $section->name;
			$model['context']['section']['id'] = $section->id;
			$model['context']['screen']['id'] = $screen->id;
			$model['context']['lesson']['id'] = $lesson->id;
			$model['context']['group']['id'] = $lesson->group->id;
			$model['context']['course']['id'] = $lesson->group->course->id;
			$model['context']['orderNumber'] = $orderNumber;
			$model['context']['id'] = $this->id;
		} else {
			$this->unsearchable();

			return [];
		}

		return $model;
	}
}
