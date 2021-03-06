<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\Concerns\GeneratesApiResponses;

class AccountStatus
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
		if ($request->session()->get('suspended')) {
			return $this->respondForbidden('Forbidden', ['account_suspended' => true]);
		}

		return $next($request);
	}
}
