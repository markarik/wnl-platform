<?php

namespace App\Models;

use Altek\Accountant\Contracts\Recordable;
use App\Events\Qna\QnaAnswerPosted;
use App\Models\Concerns\Cached;
use App\Models\Contracts\WithReactions;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use \Altek\Accountant\Recordable as RecordableTrait;

class QnaAnswer extends Model implements WithReactions, Recordable
{
	use Cached, Searchable, RecordableTrait;

	protected $fillable = ['text', 'user_id', 'question_id'];

	protected $dispatchesEvents = [
		'created' => QnaAnswerPosted::class,
	];

	public function question()
	{
		return $this->belongsTo('App\Models\QnaQuestion');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function comments()
	{
		return $this->morphMany('App\Models\Comment', 'commentable');
	}

	public function reactions()
	{
		return $this->morphToMany('App\Models\Reaction', 'reactable');
	}
}
