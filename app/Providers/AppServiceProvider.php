<?php

namespace App\Providers;

use App;
use Log;
use Validator;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\User;
use App\Observers\LessonObserver;
use App\Observers\OrderObserver;
use App\Observers\UserObserver;
use Monolog\Handler\RavenHandler;
use Illuminate\Support\Facades\Auth;
use Monolog\Formatter\LineFormatter;
use Laravel\Dusk\DuskServiceProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Tinker\TinkerServiceProvider;
use Barryvdh\Debugbar\ServiceProvider as DebugBarServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Order::observe(OrderObserver::class);
		User::observe(UserObserver::class);
		Lesson::observe(LessonObserver::class);

		if ($this->useExternalLogger()) {
			$this->addSentryLogger();
		}

		/**
		 * Custom validation rules
		 * TODO: Custom validators should have their own service provider (?)
		 */
		Validator::extend('alpha_spaces', function ($attribute, $value) {
			// Useful for names and surnames - accept letters, spaces and hyphens
			return preg_match('/^[\pL\s-]+$/u', $value);
		});
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		if ($this->app->environment('dev', 'testing')) {
			$this->app->register(DuskServiceProvider::class);
		}

		if ($this->app->environment('dev', 'local')) {
			$this->app->register(TinkerServiceProvider::class);
		}
		if (env('DEBUG_BAR') === true) {
			$this->app->register(DebugBarServiceProvider::class);
		}
	}

	public function addSentryLogger()
	{
		$handler = new RavenHandler(new \Raven_Client(env('SENTRY_DSN')));
		$handler->setFormatter(new LineFormatter("%message% %context% %extra%\n"));
		$monolog = Log::getMonolog();
		$monolog->pushHandler($handler);

		$monolog->pushProcessor(function ($record) {
			// record the current user
			$user = Auth::user();
			if ($user) {
				$record['context']['user'] = [
					'email' => $user->email,
				];
			}

			return $record;
		});
	}

	public function useExternalLogger()
	{
		return !App::environment(['dev', 'testing']) && env('LOG_LEVEL') !== 'debug';
	}
}
