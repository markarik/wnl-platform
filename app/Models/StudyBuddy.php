<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyBuddy extends Model
{
	protected $fillable = ['order_id', 'code', 'recipient', 'status'];

	protected $table = 'study_buddy';

	public function order()
	{
		return $this->hasOne('App\Models\Order');
	}

	public function coupon()
	{
		return $this->hasOne('App\Models\Coupon', 'code', 'code');
	}
}
