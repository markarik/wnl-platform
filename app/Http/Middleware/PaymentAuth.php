<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Auth;

class PaymentAuth extends Authenticate
{
	/**
	 * Get the path the user should be redirected to when they are not authenticated.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return string
	 */
	protected function redirectTo($request)
	{
		return route('payment-account');
	}
}
