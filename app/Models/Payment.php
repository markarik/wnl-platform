<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
	protected $fillable = [
		'external_id',
		'order_id',
		'status'
	];

	public function order() {
		return $this->belongsTo('App\Models\Order');
	}
}
