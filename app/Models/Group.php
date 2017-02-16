<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'course_id'];

	public function lessons()
	{
		return	$this->hasMany('\App\Models\Lesson');
	}
}
