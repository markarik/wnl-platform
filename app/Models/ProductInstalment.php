<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductInstalment extends Model
{
	protected $dates = ['due_date'];

	public function getDueDate(Order $order)
	{
		if (!is_null($this->due_date) && !is_null($this->due_days)) {
			if ($order->created_at->diffInDays($this->due_date) <= $this->due_days) {
				return $this->due_date;
			}

			return $order->created_at->addDays($this->due_days);
		}

		return $this->due_date ?? $order->created_at->addDays($this->due_days);
    }
}
