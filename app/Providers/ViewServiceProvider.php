<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
	/**
	 * Register bindings in the container.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Using class based composers...
		View::composer(
			'payment.cart', 'App\Http\View\Composers\PaymentCartComposer'
		);
	}
}
