<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
	use Cached;

    protected $fillable = ['name', 'slug', 'entry_exam_tag_id', 'entry_exam_lesson_id'];

	public function groups() {
		return $this->hasMany('\App\Models\Group');
	}

	public function courseStructureNodes() {
		return $this->hasMany('\App\Models\CourseStructureNode');
	}
}
