<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use App\Scopes\OrderByOrderNumberScope;
use App\Models\Contracts\WithTags;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use ScoutEngines\Elasticsearch\Searchable;

class Lesson extends Model implements WithTags
{
	use Cached, Searchable;

	protected $fillable = ['name', 'group_id', 'is_required'];

	const USER_LESSON_CACHE_KEY = '%s-%s-%s-user-lesson-access';
	const CACHE_VERSION = 1;

	protected static function boot() {
		parent::boot();
		static::addGlobalScope(new OrderByOrderNumberScope());
	}

	public function screens()
	{
		return $this->hasMany('\App\Models\Screen');
	}

	public function group()
	{
		return $this->belongsTo('\App\Models\Group');
	}

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}

	public function flashcardsSets() {
		return $this->hasOne('\App\Models\FlashcardsSet');
	}

	public function getQuestionsAttribute()
	{
		return QnaQuestion::whereHas('tags', function ($query) {
			$query->whereIn('tags.id', $this->tags->keyBy('id')->keys());
		})->get();
	}

	public function isAvailable($user = null)
	{
		$user = $user ?? \Auth::user();
		if ($user) {
			if ($user->isAdmin() || $user->isModerator()) {
				return true;
			}

			$lessonAccess = $this->userLessonAccess($user);
			if (!is_null($lessonAccess) && !is_null($lessonAccess->start_date)) {
				return Carbon::parse($lessonAccess->start_date)->isPast();
			}
		}

		return false;
	}

	public function isAccessible($user = null)
	{
		$user = $user ?? \Auth::user();
		if ($user) {
			if ($user->isAdmin() || $user->isModerator()) {
				return true;
			}

			$lessonAccess = $this->userLessonAccess($user);
			return !is_null($lessonAccess);
		}

		return false;
	}

	public function startDate($user = null)
	{
		$user = $user ?? \Auth::user();
		if ($user) {
			$lessonAccess = $this->userLessonAccess($user);

			if (!is_null($lessonAccess)) {
				return Carbon::parse($lessonAccess->start_date);
			}
		}

		return null;
	}

	public function userLessonAccess(User $user) {
		return DB::table('user_lesson')
			->where('lesson_id', $this->id)
			->where('user_id', $user->id)
			->first();
	}
}
