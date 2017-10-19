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

	public function availability()
	{
		return $this->hasMany('App\Models\LessonAvailability');
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

	public function isAvailable($editionId)
	{
		$user = \Auth::user();
		$lessonAccess = $user->lessonsAccess()->where('lesson_id', $this->id)->first();
		if ($lessonAccess) {
			return true;
		}

		$availability = $this->availability->where('edition_id', $editionId)->first();
		if (!is_null($availability)) {
			return $availability->start_date->isPast();
		}

		return false;
	}

	public function startDate($editionId)
	{
		$availability = $this->availability->where('edition_id', $editionId)->first();

		if (!is_null($availability)) {
			return $availability->start_date;
		}

		return null;
	}
}
