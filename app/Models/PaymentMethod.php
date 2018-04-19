<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
	protected $fillable = ['slug'];

	public function isAvailable()
	{
		return now()->between($this->pivot->start_date, $this->pivot->end_date);
	}
}
