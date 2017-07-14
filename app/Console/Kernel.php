<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		Commands\AddressesExport::class,
		Commands\ArchiveChatMessages::class,
		Commands\CancelOrder::class,
		Commands\CategoriesTags::class,
		Commands\ChangeOrderPaymentMethod::class,
		Commands\DropTables::class,
		Commands\DumpCourseStructure::class,
		Commands\EncryptPasswords::class,
		Commands\FlushCacheByTag::class,
		Commands\IssueFinalInvoice::class,
		Commands\InvoicesExport::class,
		Commands\LessonTags::class,
		Commands\ListOrders::class,
		Commands\MigrateUserData::class,
		Commands\MarkOrderAsPaid::class,
		Commands\MarkWrongQuestionsAsBookmarked::class,
		Commands\OptimaExport::class,
		Commands\OrdersExport::class,
		Commands\QuizImport::class,
		Commands\PopulateAmountColumns::class,
		Commands\SectionsUpdate::class,
		Commands\SlideshowsRemove::class,
		Commands\SlidesFromCategory::class,
		Commands\SlidesImport::class,
		Commands\StoreProgress::class,
		Commands\StoreTime::class,
		Commands\IssueFinalInvoice::class,
		Commands\CategoriesTags::class,
		Commands\LessonTags::class,
		Commands\MegaUltraSuperDuperChartUpdateScript::class,
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
			->command('cache:warmup')
			->dailyAt('00:30');
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
