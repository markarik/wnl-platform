<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderInstalment extends Model
{
	protected $fillable = [
		'order_id', 'due_date', 'order_number', 'amount', 'paid_amount'
	];

	protected $dates = ['due_date'];

	protected $appends = ['left_amount', 'paid'];

	public function getLeftAmountAttribute()
	{
		return (float)$this->amount - $this->paid_amount;
	}

	public function getPaidAttribute()
	{
		return $this->amount === $this->paid_amount;
	}
}
