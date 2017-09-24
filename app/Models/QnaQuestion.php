<?php

namespace App\Models;

use App\Events\Qna\QuestionPosted;
use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class QnaQuestion extends Model
{
	use Cached, Searchable;

	protected $fillable = ['text', 'user_id', 'meta'];

	protected $casts = [
		'meta' => 'array',
	];

	protected $events = [
		'created' => QuestionPosted::class,
	];

	public function answers()
	{
		return $this->hasMany('App\Models\QnaAnswer', 'question_id');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}

	public function reactions()
	{
		return $this->morphToMany('App\Models\Reaction', 'reactable');
	}

	public function getScreenAttribute()
	{
		$screen = Screen::select();

		foreach ($this->tags as $tag) {
			$screen->whereHas('tags', function ($query) use ($tag) {
				$query->where('tags.id', $tag->id);
			});
		}

		return $screen->first();
	}
}
