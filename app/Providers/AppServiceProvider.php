<?php
namespace App\Providers;
use App;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\Notification;
use App\Models\Order;
use App\Models\QnaAnswer;
use App\Models\QnaQuestion;
use App\Models\QuizQuestion;
use App\Models\Slide;
use App\Models\User;
use App\Observers\CommentObserver;
use App\Observers\LessonObserver;
use App\Observers\NotificationModelObserver;
use App\Observers\NotificationObserver;
use App\Observers\OrderObserver;
use App\Observers\QnaAnswerObserver;
use App\Observers\QnaQuestionObserver;
use App\Observers\QuizQuestionObserver;
use App\Observers\SlideObserver;
use App\Observers\UserObserver;
use Barryvdh\Debugbar\ServiceProvider as DebugBarServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use Laravel\Tinker\TinkerServiceProvider;
use Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RavenHandler;
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
		$this->registerModelObservers();
		$this->registerSentryLogger();
		$this->registerCustomValidators();
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
	public function registerSentryLogger()
	{
		if (!$this->useExternalLogger()) return;
		$handler = new RavenHandler(new \Raven_Client(env('SENTRY_DSN')));
		$handler->setFormatter(new LineFormatter("%message% %context% %extra%\n"));
		$handler->setLevel(env('APP_LOG_LEVEL'));
		$monolog = Log::getMonolog();
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
		Order::observe(OrderObserver::class);
		User::observe(UserObserver::class);
		Lesson::observe(LessonObserver::class);
		Notification::observe(NotificationObserver::class);
		QnaQuestion::observe(QnaQuestionObserver::class);
		QnaAnswer::observe(QnaAnswerObserver::class);
		QnaAnswer::observe(NotificationModelObserver::class);
		QnaQuestion::observe(NotificationModelObserver::class);
		Comment::observe(NotificationModelObserver::class);
		Comment::observe(CommentObserver::class);
		QuizQuestion::observe(QuizQuestionObserver::class);
		Slide::observe(SlideObserver::class);
	}
	protected function registerCustomValidators()
	{
		Validator::extend('alpha_spaces', function ($attribute, $value) {
			// Useful for names and surnames - accept letters, spaces and hyphens
			return preg_match('/^[\pL\s-]+$/u', $value);
		});

		Validator::extend('alpha_comas', function ($attribute, $value) {
			// Useful for textareas - accepts letters, comas, dots, spaces and hyphens
			return preg_match('/^[\pL\s-,.]+$/u', $value);
		});
	}
}
