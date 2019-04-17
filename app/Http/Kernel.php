<?php

namespace App\Http;

use App\Http\Middleware\AccountStatus;
use App\Http\Middleware\Admin;
use App\Http\Middleware\ApiAuth;
use App\Http\Middleware\CheckIfAppUnavailableMode;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\PaymentAuth;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfPaid;
use App\Http\Middleware\SignupsOpen;
use App\Http\Middleware\Subscription;
use App\Http\Middleware\TermsOfUse;
use App\Http\Middleware\VerifyCsrfToken;
use Barryvdh\Cors\HandleCors;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel
{
	/**
	 * The application's global HTTP middleware stack.
	 *
	 * These middleware are run during every request to your application.
	 *
	 * @var array
	 */
	protected $middleware = [
		CheckForMaintenanceMode::class,
	];

	/**
	 * The application's route middleware groups.
	 *
	 * @var array
	 */
	protected $middlewareGroups = [
		'web' => [
			EncryptCookies::class,
			AddQueuedCookiesToResponse::class,
			StartSession::class,
			ShareErrorsFromSession::class,
			VerifyCsrfToken::class,
			SubstituteBindings::class,
		],

		'api' => [
			'throttle:60,1',
			'bindings',
			HandleCors::class,
		],

		'hooks' => [],

		'papi' => [
			EncryptCookies::class,
			AddQueuedCookiesToResponse::class,
			StartSession::class,
			ShareErrorsFromSession::class,
			VerifyCsrfToken::class,
			SubstituteBindings::class,
		],
	];

	/**
	 * The application's route middleware.
	 *
	 * These middleware may be assigned to groups or used individually.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
		'auth' => Authenticate::class,
		'admin' => Admin::class,
		'payment-auth' => PaymentAuth::class,
		'auth.basic' => AuthenticateWithBasicAuth::class,
		'bindings' => SubstituteBindings::class,
		'can' => Authorize::class,
		'guest' => RedirectIfAuthenticated::class,
		'throttle' => ThrottleRequests::class,
		'payment' => RedirectIfPaid::class,
		'signups-open' => SignupsOpen::class,
		'api-auth' => ApiAuth::class,
		'subscription' => Subscription::class,
		'terms' => TermsOfUse::class,
		'account-status' => AccountStatus::class,
	];

	public function bootstrap()
	{
		parent::bootstrap();

		if ($this->app->environment() == 'demo') {
			$this->pushMiddleware(CheckIfAppUnavailableMode::class);
		}
	}
}
