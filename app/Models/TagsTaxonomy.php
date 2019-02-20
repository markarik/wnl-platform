<?php

namespace App\Models;

use App\Scopes\OrderByOrderNumberScope;
use Illuminate\Database\Eloquent\Model;

class TagsTaxonomy extends Model
{
	public $timestamps = false;
	protected $fillable = ['parent_tag_id', 'tag_id', 'taxonomy_id', 'order_number'];
	protected $table = 'tags_taxonomy';

	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope(new OrderByOrderNumberScope());
	}

	public function parentTag()
	{
		return $this->belongsTo('App\Models\Tag', 'parent_tag_id');
	}

	public function tag()
	{
		return $this->belongsTo('App\Models\Tag', 'tag_id');
	}

	public function taxonomy()
	{
		return $this->belongsTo('App\Models\Taxonomy');
	}
}
