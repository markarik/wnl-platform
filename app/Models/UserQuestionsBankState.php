<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuestionsBankState extends Model
{
	protected $table = 'user_questions_bank_state';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['user_id', 'key', 'value'];

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
}
