<?php

namespace App\Console;

use App\Models\Comment;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Artisan;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		Commands\AddressesExport::class,
		Commands\AddContext::class,
		Commands\ArchiveChatMessages::class,
		Commands\CancelOrder::class,
		Commands\CategoriesTags::class,
		Commands\ChangeOrderPaymentMethod::class,
		Commands\CheckQuizQuestions::class,
		Commands\CreateTaxonomy::class,
		Commands\DropTables::class,
		Commands\DumpCourseStructure::class,
		Commands\EncryptPasswords::class,
		Commands\FlushCacheByTag::class,
		Commands\ImportTaxonomies::class,
		Commands\IssueFinalInvoice::class,
		Commands\InvoicesExport::class,
		Commands\LessonTags::class,
		Commands\ListOrders::class,
		Commands\MigrateUserData::class,
		Commands\MarkOrderAsPaid::class,
		Commands\MarkWrongQuestionsAsBookmarked::class,
		Commands\MegaUltraSuperDuperChartUpdateScript::class,
		Commands\OptimaExport::class,
		Commands\OrdersExport::class,
		Commands\QuizImport::class,
		Commands\PopulateAmountColumns::class,
		Commands\SectionsUpdate::class,
		Commands\SlideshowsRemove::class,
		Commands\SlidesFromCategory::class,
		Commands\SlidesImport::class,
		Commands\SlidesSnippets::class,
		Commands\StoreProgress::class,
		Commands\StoreTime::class,
		Commands\TagsCleanup::class,
		Commands\TagsFromTaxonomies::class,
		Commands\TaxonomizeTags::class,
		Commands\WarmUpCache::class,
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule $schedule
	 *
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule
			->command('chat:archive-messages')
			->hourly();

		$schedule
			->command("scout:import 'App\\Models\\Slide'")
			->dailyAt('00:30')
			->after(function () use ($schedule) {
				Artisan::call('cache:clear', [
					'--tags' => 'api,slides,search',
				]);
			})
			->after(function () use ($schedule) {
				Artisan::call('cache:warmup');
			});

		$schedule
			->command('time:store')
			->dailyAt('01:30');

		$schedule
			->command('progress:store')
			->dailyAt('02:30');
	}

	/**
	 * Register the Closure based commands for the application.
	 *
	 * @return void
	 */
	protected function commands()
	{
		require base_path('routes/console.php');
	}
}
