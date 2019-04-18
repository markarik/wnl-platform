<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to your controller routes.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'App\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function boot()
	{
		parent::boot();
	}

	/**
	 * Define the routes for the application.
	 *
	 * @return void
	 */
	public function map()
	{
		$this->mapApiRoutes();
		$this->mapWebRoutes();
		$this->mapWebHookRoutes();
		$this->mapPapi2Routes();
	}

	/**
	 * Define the "web" routes for the application.
	 *
	 * These routes all receive session state, CSRF protection, etc.
	 *
	 * @return void
	 */
	protected function mapWebRoutes()
	{
		if (\App::environment('demo')) {
			$path = 'demo/routes/web.php';
		} else {
			$path = 'routes/web.php';
		}
		Route::group([
			'middleware' => 'web',
			'namespace' => $this->namespace,
		], function ($router) use ($path) {
			require base_path($path);
		});
	}

	/**
	 * Define the "api" routes for the application.
	 *
	 * These routes are typically stateless.
	 *
	 * @return void
	 */
	protected function mapApiRoutes()
	{
		Route::group([
			'middleware' => 'api',
			'namespace' => $this->namespace,
			'prefix' => 'api/v1',
		], function ($router) {
			require base_path('routes/api.php');
		});
	}

	private function mapWebHookRoutes()
	{
		Route::group([
			'middleware' => 'hooks',
			'namespace' => $this->namespace,
		], function ($router) {
			require base_path('routes/hooks.php');
		});
	}

	private function mapPapi2Routes()
	{
		Route::group([
			'middleware' => 'papi',
			'namespace' => $this->namespace,
			'prefix' => 'papi/v2',
		], function ($router) {
			require base_path('routes/papi.php');
		});
	}
}
