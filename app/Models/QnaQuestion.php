<?php

namespace App\Models;

use Altek\Accountant\Contracts\Recordable;
use App\Events\Qna\QnaQuestionPosted;
use App\Models\Concerns\Cached;
use App\Models\Contracts\WithReactions;
use App\Models\Contracts\WithTags;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use \Altek\Accountant\Recordable as RecordableTrait;

class QnaQuestion extends Model implements WithReactions, WithTags, Recordable
{
	use Cached, Searchable, SoftDeletes, RecordableTrait;

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
