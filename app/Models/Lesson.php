<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use App\Models\Contracts\WithTags;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use ScoutEngines\Elasticsearch\Searchable;

class Lesson extends Model implements WithTags
{
	use Cached, Searchable;

	protected $fillable = ['name', 'is_required'];

	/** @var Carbon */
	private $startDate = null;
	private $startDateLoaded = false;

	const USER_LESSON_CACHE_KEY = '%s-%s-%s-user-lesson-access';
	const CACHE_VERSION = 1;

	public function screens()
	{
		return $this->hasMany('\App\Models\Screen');
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

	public function isAvailable(User $user = null)
	{
		$user = $user ?? Auth::user();

		if (is_null($user)) {
			return false;
		}

		if ($user->isAdmin() || $user->isModerator()) {
			return true;
		}

		$startDate = $this->getStartDate($user);

		if (!is_null($startDate)) {
			return $startDate->isPast();
		} else {
			return false;
		}
	}

	public function isAccessible(User $user = null)
	{
		$user = $user ?? Auth::user();

		if (is_null($user)) {
			return false;
		}

		if ($user->isAdmin() || $user->isModerator()) {
			return true;
		}

		return !is_null($this->getStartDate($user));
	}

	public function isDefaultStartDate(User $user = null)
	{
		$user = $user ?? Auth::user();

		if (is_null($user)) {
			return false;
		}

		$lesson = $user->getLessonsAvailability()->filter(function ($lesson) {
			return $lesson->id === $this->id;
		})->first();

		return $lesson ? (bool) $lesson->is_default_start_date : true;
	}

	/**
	 * TODO move it away from Lesson model
	 *
	 * @param User|null $user
	 * @return Carbon|null
	 */
	public function getStartDate(User $user = null)
	{
		$user = $user ?? Auth::user();

		if (is_null($user)) {
			return null;
		}

		$lesson = $user->getLessonsAvailability()->filter(function (Lesson $lesson) {
			return $lesson->id === $this->id;
		})->first();

		if (!$lesson) {
			return null;
		}

		// No pivot for LessonProduct, pivot for UserLesson
		return Carbon::parse($lesson->lesson_product_start_date ?? $lesson->pivot->start_date);
	}
}
