<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
	protected $table = 'user_subscription';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'access_start', 'access_end', 'user_id'
	];

	/**
	 * Relationships
	 */
	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
}
