<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PaymentMethodProduct extends Pivot
{
	public function getStartDateAttribute()
	{
		$date = $this->attributes['start_date'];

		return $date ? Carbon::parse($date) : Carbon::yesterday();
	}

	public function getEndDateAttribute()
	{
		$date = $this->attributes['end_date'];

		return $date ? Carbon::parse($date) : Carbon::tomorrow();
	}
}
