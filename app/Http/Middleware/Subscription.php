<?php

namespace App\Http\Middleware;

use App\Models\UserSubscription;
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
		$status = $user->subscription_proxy->subscription_status ?? UserSubscription::SUBSCRIPTION_STATUS_INACTIVE;

		if ($status === UserSubscription::SUBSCRIPTION_STATUS_ACTIVE) {
			return $next($request);
		}

		return $this->respondForbidden(['subscription_status' => $status]);
	}

}
