<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\Concerns\GeneratesApiResponses;

class Subscription
{
	use GeneratesApiResponses;

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$user = Auth::user();
		$status = $user->subscription->subscription_status ?? 'inactive';

		if ($status === 'active') {
			return $next($request);
		}

		return $this->respondForbidden(['subscription_status' => $status]);
	}

}
