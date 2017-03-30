<?php


namespace App\Http\Middleware;


use App\Http\Controllers\Api\Concerns\GeneratesApiResponses;
use Illuminate\Support\Facades\Auth;

class ApiAuth
{
	use GeneratesApiResponses;

	public function handle($request, \Closure $next)
	{
		if (!Auth::check()) return $this->respondUnauthorized();

		return $next($request);
	}
}
