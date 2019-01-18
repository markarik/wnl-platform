<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class CourseStructureNode extends Model
{
	use NodeTrait;

	protected $fillable = ['name', 'type'];
}
