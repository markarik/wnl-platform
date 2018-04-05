<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResults extends Model
{
	protected $fillable = ['exam_tag_id', 'user_id', 'correct', 'correct_percentage', 'resolved', 'resolved_percentage', 'details', 'total'];
	protected $table = 'exams_results';

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
}
