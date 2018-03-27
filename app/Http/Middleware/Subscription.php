<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\Concerns\GeneratesApiResponses;

class Subscription
{
	use GeneratesApiResponses;

	const CACHE_KEY = '%s-%s-subscription-status';
	const CACHE_VER = '1';

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

		$products = Cache::remember($this->key($user), 60 * 24, function () {
			return \DB::table('products')->get();
		});

		dd($user->products);

		if ('elo') {
			return $this->respondForbidden();
		}

		return $next($request);
	}

	protected function key($user)
	{
		return sprintf(self::CACHE_KEY, self::CACHE_VER, $user->id);
	}
}
