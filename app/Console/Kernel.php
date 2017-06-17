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
        Commands\DropTables::class,
		Commands\EncryptPasswords::class,
		Commands\ListOrders::class,
		Commands\MarkOrderAsPaid::class,
		Commands\CancelOrder::class,
		Commands\ChangeOrderPaymentMethod::class,
		Commands\PopulateAmountColumns::class,
		Commands\OptimaExport::class,
		Commands\ArchiveChatMessages::class,
		Commands\AddressesExport::class,
		Commands\MigrateUserData::class,
		Commands\QuizImport::class,
		Commands\SlidesImport::class,
		Commands\DumpCourseStructure::class,
		Commands\WarmUpCache::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
		$schedule
			->command('chat:archive-messages')
			->hourly();
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
