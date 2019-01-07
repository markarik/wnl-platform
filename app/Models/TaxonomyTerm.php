<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

/**
 * Class TaxonomyTerm
 * @package App\Models
 *
 * NodeTrait Docs: https://github.com/lazychaser/laravel-nestedset
 */
class TaxonomyTerm extends Model
{
	use NodeTrait;

	protected $fillable = ['description'];

	public function taxonomy() {
		return $this->belongsTo('App\Models\Taxonomy');
	}

	public function tag() {
		return $this->belongsTo('App\Models\Tag');
	}
}
