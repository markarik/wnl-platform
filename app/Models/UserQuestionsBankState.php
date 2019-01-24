<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuestionsBankState extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['user_id', 'key', 'value'];

	protected $casts = [
		'user_id' => 'int',
	];

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
}
