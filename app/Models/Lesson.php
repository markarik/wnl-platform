<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
	use Cached;

	protected $fillable = ['name', 'group_id'];

	public function screens()
	{
		return $this->hasMany('\App\Models\Screen');
	}

	public function group()
	{
		return $this->belongsTo('\App\Models\Group');
	}

	public function userAvailability()
	{
		return $this->hasMany('App\Models\UserLessonAvailability');
	}

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}

	public function getQuestionsAttribute()
	{
		return QnaQuestion::whereHas('tags', function ($query) {
			$query->whereIn('tags.id', $this->tags->keyBy('id')->keys());
		})->get();
	}

	public function isAvailable($editionId = 1)
	{
		$user = \Auth::user();
		if ($user) {
			$lessonAccess = $this->userAvailability->where('user_id', $user->id)->first();
			if (!is_null($lessonAccess)) {
				return $lessonAccess->start_date->isPast();
			}
		}

		return false;
	}

	public function isAccessible($editionId = 1)
	{
		$user = \Auth::user();
		if ($user) {
			$lessonAccess = $this->userAvailability->where('user_id', $user->id)->first();
			return !is_null($lessonAccess);
		}

		return false;
	}

	public function startDate($editionId)
	{
		$user = \Auth::user();
		if ($user) {
			$lessonAccess = $this->userAvailability->where('user_id', $user->id)->first();
			if (!is_null($lessonAccess)) {
				return $lessonAccess->start_date;
			}
		}

		return null;
	}
}
