<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\QnaAnswer;
use App\Models\User;
use App\Observers\CommentObserver;
use App\Observers\LessonObserver;
use App\Observers\OrderObserver;
use App\Observers\QnaAnswerObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use Barryvdh\Debugbar\ServiceProvider as DebugBarServiceProvider;
use Laravel\Tinker\TinkerServiceProvider;
use Log;
use Validator;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RavenHandler;

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
		QnaAnswer::observe(QnaAnswerObserver::class);
		Comment::observe(CommentObserver::class);

		// Send slack notifications when a critical or higher level error occurs
//		$monolog = Log::getMonolog();
//		$token = env('ERROR_REPORTER_SLACK_TOKEN');
//		$chanel = env('ERROR_REPORTER_SLACK_CHANNEL');
//		$level = \Monolog\Logger::CRITICAL;
//		$slackHandler = new \Monolog\Handler\SlackHandler($token, $chanel, 'Error Reporter', true, null, $level);
//		$monolog->pushHandler($slackHandler);

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

		/**
		 * Custom validation rules
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
		if (env('APP_TESTING') === true) {
			$this->app->register(DuskServiceProvider::class);
		}
		if ($this->app->environment('testing', 'dev', 'local')) {
			$this->app->register(TinkerServiceProvider::class);
		}
		if (env('DEBUG_BAR') === true) {
			$this->app->register(DebugBarServiceProvider::class);
		}
	}
}
