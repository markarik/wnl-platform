<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfAppUnavailableMode
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		if (env('APP_UNAVAILABLE')) {
			return response()->view('app_unavailable', [], 404);
		}

		return $next($request);
	}
}
