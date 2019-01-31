<?php
namespace App\Providers;

use App;
use App\Models;
use App\Observers;
use Barryvdh\Debugbar\ServiceProvider as DebugBarServiceProvider;
use Bschmitt\Amqp\AmqpServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use Laravel\Tinker\TinkerServiceProvider;
use Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RavenHandler;
use Monolog\Logger;
use Validator;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->forceHttpsLinks();
		$this->registerModelObservers();
		$this->registerSentryLogger();
		$this->registerCustomValidators();
		$this->registerQueueLogger();
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
			$this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
		}
		if (env('DEBUG_BAR') === true) {
			$this->app->register(DebugBarServiceProvider::class);
		}
		if($this->app->runningInConsole()) {
			$this->app->register(AmqpServiceProvider::class);
			$this->app->register(DiscussableServiceProvider::class);
		}
	}

	public function registerSentryLogger()
	{
		if (!$this->useExternalLogger()) return;

		$level = Logger::INFO;
		$handler = new RavenHandler(new \Raven_Client(env('SENTRY_DSN')), $level);
		$handler->setFormatter(new LineFormatter("%message% %context% %extra%\n"));
		$monolog = Log::getLogger();
		$monolog->pushHandler($handler);
		$monolog->pushProcessor(function ($record) {
			// record app version
			$record['context']['app_version'] = config('app.version');

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
		return !App::environment(['dev', 'testing']);
	}

	protected function registerModelObservers()
	{
		Models\Order::observe(Observers\OrderObserver::class);
		Models\User::observe(Observers\UserObserver::class);
		Models\Lesson::observe(Observers\LessonObserver::class);
		Models\Notification::observe(Observers\NotificationObserver::class);
		Models\QnaQuestion::observe(Observers\QnaQuestionObserver::class);
		Models\QnaAnswer::observe(Observers\QnaAnswerObserver::class);
		Models\QnaAnswer::observe(Observers\NotificationModelObserver::class);
		Models\QnaQuestion::observe(Observers\NotificationModelObserver::class);
		Models\Comment::observe(Observers\NotificationModelObserver::class);
		Models\Comment::observe(Observers\CommentObserver::class);
		Models\QuizQuestion::observe(Observers\QuizQuestionObserver::class);
		Models\Slide::observe(Observers\SlideObserver::class);
		Models\Task::observe(Observers\TaskObserver::class);
		Models\UserProfile::observe(Observers\UserProfileObserver::class);
	}

	protected function registerCustomValidators()
	{
		Validator::extend('alpha_spaces', function ($attribute, $value) {
			// Useful for names and surnames - accept letters, spaces and hyphens
			return preg_match('/^[\pL\s-]+$/u', $value);
		});

		Validator::extend('alpha_comas', function ($attribute, $value) {
			// Useful for textareas - accepts letters, comas, dots, spaces and hyphens
			return preg_match('/^[\pL\s\d-,.:;()""]+$/u', $value);
		});

		Validator::extend('morph_exists', function ($attribute, $value, $parameters, $validator) {
			if (!$type = array_get($validator->getData(), $parameters[0], false)) {
				return false;
			}

			if (Relation::getMorphedModel($type)) {
				$type = Relation::getMorphedModel($type);
			}

			if (!class_exists($type)) {
				return false;
			}

			return resolve($type)->whereKey($value)->exists();
		});
	}

	protected function registerQueueLogger()
	{
		Queue::failing(function (JobFailed $event) {
			app('sentry')->captureException($event->exception, [
				'extra' => ['app_version' => config('app.version')],
				'job' => $event->job->resolveName(),
			]);
		});
	}

	private function forceHttpsLinks() {
		$url = app(UrlGenerator::class);
		if (env('APP_ENV') !== 'dev') {
			$url->forceScheme('https');
		}
	}
}
