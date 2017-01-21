<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfPaid
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
		$user = Auth::user();

		if (!$user || !$request->isMethod('get')) {
			return $next($request);
		}

		if ($user->orders->count() < 1){
			return $next($request);
		}

		if ($user->orders()->recent()->paid){
			return redirect()->route('profile-orders');
		}

		return $next($request);
    }
}
