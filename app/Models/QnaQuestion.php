<?php

namespace App\Models;

use App\Events\Qna\QnaQuestionPosted;
use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class QnaQuestion extends Model
{
	use Cached, Searchable, SoftDeletes;

	protected $fillable = ['text', 'user_id', 'meta', 'discussion_id'];

	protected $casts = [
		'meta' => 'array',
	];

	protected $dispatchesEvents = [
		'created' => QnaQuestionPosted::class,
	];

	protected $dates = ['deleted_at'];

	public function discussion() {
		return $this->belongsTo('App\Models\Discussion');
	}

	public function qnaAnswers()
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

	public function getPageAttribute()
	{
		$page = Page::select();

		foreach ($this->tags as $tag) {
			$page->whereHas('tags', function ($query) use ($tag) {
				$query->where('tags.id', $tag->id);
			});
		}

		return $page->first();
	}
}
