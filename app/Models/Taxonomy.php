<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model
{
	protected $fillable = ['name'];

	public function tagsTaxonomy() {
		return $this->hasMany('App\Models\TagsTaxonomy');
	}
}
