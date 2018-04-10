<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderInstalment extends Model
{
    protected $fillable = [
    	'order_id', 'due_date', 'paid', 'order_number', 'amount', 'paid_amount'
	];

    protected $casts = [
    	'paid' => 'boolean'
	];

    protected $dates = ['due_date'];
}
