<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\SlidesApiController;
use App\Models\Slide;
use Lib\SlideParser\Parser;
use Illuminate\Console\Command;

class MigrateSlidesImages extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slides:migrateImages';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'PLAT-732 - we had a problem with migrating images with UTF8 special charaters into our S3.
	 This command finds all affected slides and performs the migration';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$slidesToMigrate = Slide::where('content', 'like', '%slid.es%')->get();
		$bar = $this->output->createProgressBar($slidesToMigrate->count());

		foreach ($slidesToMigrate as $slide) {
			$bar->advance();

			$parser = new Parser;
			$content = $parser->handleCharts($slide->content);
			$content = $parser->handleImages($content);

			$slide->update([
				'content'       => $content,
			]);

			SlidesApiController::slideCacheForget($slide);
		}

		$bar->finish();
		print PHP_EOL;

		$this->info('Done.');

		return;
	}
}
