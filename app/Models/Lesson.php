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

	private function userLessonAccess(User $user) {
		$userLesson = DB::table('user_lesson')
			->where('lesson_id', $this->id)
			->where('user_id', $user->id)
			->first();

		if ($userLesson) {
			return $userLesson;
		}

		// FIXME this is suboptimal to run on every lesson
		// Rethink and run once
		// Also, use Eloquent where possible
		$newestProductBought = DB::select(DB::raw(
			'select products.id from products join orders on orders.product_id = products.id join lesson_product on lesson_product.product_id = products.id where orders.user_id = :user_id and orders.paid = 1 group by products.id order by course_start desc limit 1;'
		), ['user_id' => $user->id]);

		if (count($newestProductBought) < 1) {
			return null;
		}

		$lessonProduct = DB::select(DB::raw(
			'select start_date from lesson_product where product_id = :product_id and lesson_id = :lesson_id;'
		), [
			'lesson_id' => $this->id,
			'product_id' => $newestProductBought[0]->id
		]);

		if (count($lessonProduct) < 1) {
			return null;
		}

		return $lessonProduct[0];
	}
}
