<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use App\Scopes\OrderByOrderNumberScope;
use App\Models\Contracts\WithTags;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use ScoutEngines\Elasticsearch\Searchable;

class Lesson extends Model implements WithTags
{
	use Cached, Searchable;

	protected $fillable = ['name', 'group_id', 'is_required'];

	/** @var Carbon */
	private $startDate = null;
	private $startDateLoaded = false;

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

	public function isAvailable()
	{
		$user = \Auth::user();

		if ($user->isAdmin() || $user->isModerator()) {
			return true;
		}

		$startDate = $this->startDate();

		if (!is_null($startDate)) {
			return $startDate->isPast();
		} else {
			return false;
		}
	}

	public function isAccessible()
	{
		$user = \Auth::user();

		if ($user->isAdmin() || $user->isModerator()) {
			return true;
		}

		return !is_null($this->startDate());
	}

	public function startDate()
	{
		if ($this->startDateLoaded) {
			return $this->startDate;
		}

		$user = \Auth::user();

		$userLesson = UserLesson::where([
			'lesson_id' => $this->id,
			'user_id' => $user->id
		])->first();

		if ($userLesson) {
			return $this->setStartDate($userLesson->start_date);
		}

		$productId = $user->getProductIdForDefaultLessonsStartDates();

		if (!$productId) {
			return $this->setStartDate(null);
		}

		$lessonProduct = LessonProduct::where([
			'lesson_id' => $this->id,
			'product_id' => $productId
		])->first();

		if ($lessonProduct) {
			return $this->setStartDate($lessonProduct->start_date);
		}

		return $this->setStartDate(null);
	}

	private function setStartDate($startDate) {
		$this->startDate = $startDate;
		$this->startDateLoaded = true;

		return $startDate;
	}
}
