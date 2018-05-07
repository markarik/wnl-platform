<?php

namespace App\Http\Middleware;

use Closure;

class TermsOfUse
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
    	if (!\Auth::user()->consent_terms) {
    		return redirect()->route('terms');
		}

        return $next($request);
    }
}
