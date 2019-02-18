<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use App\Models\Concerns\WithTags;
use Illuminate\Database\Eloquent\Model;

class Screen extends Model implements WithTags
{
	use Cached;

	protected $casts = [
		'meta' => 'array',
	];

	protected $fillable = [
		'content', 'type', 'name', 'meta', 'lesson_id',
		'order_number', 'id', 'discussion_id', 'is_discussable'
	];

	public function lesson()
	{
		return $this->belongsTo('\App\Models\Lesson');
	}

	public function sections()
	{
		return $this->hasMany('\App\Models\Section');
	}

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}

	public function discussion()
	{
		return $this->belongsTo('\App\Models\Discussion');
	}

	public function getSlideshowAttribute()
	{
		if (!empty($this->meta)) {
			$metaResources = collect($this->meta['resources'])->keyBy('name');
			$slideshowResource = $metaResources->get('slideshows');

			return Slideshow::find($slideshowResource['id']);
		}

		return null;
	}
}
