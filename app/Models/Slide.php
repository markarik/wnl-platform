<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use App\Models\Contracts\WithReactions;
use App\Models\Contracts\WithTags;
use Facades\App\Contracts\CourseProvider;
use Lib\SlideParser\Parser;
use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

class Slide extends Model implements WithReactions, WithTags
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
		$snippet = (new Parser())->createSnippet($value);
		$this->attributes['snippet'] = json_encode($snippet);
	}

	public function toSearchableArray()
	{
		$model = $this->toArray();
		$model['context'] = [];

		if (!empty($this->sections) && !empty($this->sections->first())) {
			$section = $this->sections->first();
			$screen = $section->screen ?? null;
			$lesson = $screen->lesson ?? null;

			if (!$screen || !$lesson) {
				// Don't index slide if it doesn't have
				// a parent lesson or screen.
				return [];
			}
			$orderNumber = (int)Presentable::where([
				['presentable_type', '=', 'App\\Models\\Slideshow'],
				['presentable_id', '=', $screen->slideshow->id],
				['slide_id', '=', $this->id],
			])->first()->order_number;

			$model['context']['section']['name'] = $section->name;
			$model['context']['section']['id'] = $section->id;
			$model['context']['screen']['id'] = $screen->id;
			$model['context']['lesson']['id'] = $lesson->id;
			$model['context']['course']['id'] = CourseProvider::getCourseId();
			$model['context']['orderNumber'] = $orderNumber;
			$model['context']['id'] = $this->id;
		} else {
			$this->unsearchable();

			return [];
		}

		return $model;
	}
}
