<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class Screen extends Model
{
	use Cached;

	protected $casts = [
		'meta' => 'json',
	];

	protected $fillable = ['content', 'type', 'name', 'meta', 'lesson_id', 'order_number'];

	public function lesson()
	{
		return $this->belongsTo('\App\Models\Lesson');
	}

	public function sections()
	{
		return $this->hasMany('\App\Models\Section');
	}

	public function getSlideshowAttribute()
	{
		$metaResources = collect($this->meta['resources'])->keyBy('name');
		$slideshowResource = $metaResources->get('slideshows');

		return Slideshow::find($slideshowResource['id']);
	}
}
