<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class CourseStructureNode extends Model
{
	use NodeTrait;

	protected $fillable = ['course_id', 'structurable_type', 'structurable_id'];

	public function structurable()
	{
		return $this->morphTo();
	}
}
