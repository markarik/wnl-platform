<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Auth;

class Admin extends Authenticate
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @param  string[] ...$guards
	 * @return mixed
	 */
	public function handle($request, Closure $next, ...$guards)
	{
		$this->authenticate($guards);

		abort_unless(Auth::user()->isAdmin(), 403);

		return $next($request);
	}
}
