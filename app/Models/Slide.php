<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;
use Facades\Lib\SlideParser\Parser;
use Laravel\Scout\Searchable;

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

	public function screens()
	{
		return $this->morphedByMany('\App\Models\Screen', 'presentable');
	}

	public function sections()
	{
		return $this->morphedByMany('\App\Models\Section', 'presentable');
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

		if (!empty($this->sections) && !empty($this->sections->first())) {
			$section = $this->sections->first();
			$screen = $section->screen;
			$lesson = $screen->lesson;
			$model['section']['id'] = $section->name;
			$model['section']['id'] = $section->id;
			$model['screen']['id'] = $screen->id;
			$model['lesson']['id'] = $lesson->id;
			$model['group']['id'] = $lesson->group->id;
		}

		return $model;
	}
}
