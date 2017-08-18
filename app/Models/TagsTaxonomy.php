<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagsTaxonomy extends Model
{
	protected $fillable = ['parent_tag_id', 'tag_id', 'taxonomy_id', 'order_number'];
    protected $table = 'tags_taxonomy';
    public $timestamps = false;

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
