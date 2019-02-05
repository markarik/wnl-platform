<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\CoursesApiController;
use Illuminate\Console\Command;
use App\Models\Screen;
use App\Models\Section;
use App\Models\Slide;
use App\Models\Slideshow;
use App\Models\Subsection;
use App\Models\Presentable;

class SlideshowsRemove extends Command
{
	const SCREENS_DELIMITER = ',';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slideshows:remove {screensIds}';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$screensIds = explode(self::SCREENS_DELIMITER, $this->argument('screensIds'));

		if (count($screensIds) === 0) die("Screen with ID $screensIds does not exist.\n");

		$confirm = "You are about to remove screens " . $this->argument('screensIds') . ", with all slides, tags etc. Are you absolutely sure?";

		if ($this->confirm($confirm)) {
			$bar = $this->output->createProgressBar(count($screensIds));

			foreach ($screensIds as $screenId) {
				$bar->advance();

				$screen = Screen::find($screenId);

				if (!$screen) {
					$this->error("Screen {$screenId} does not exist\n\n");
					continue;
				}

				if ($screen->type !== 'slideshow') {
					$this->error("Screen {$screenId} does not contain a slideshow. It's type is '{$screen->type}'\n\n");
					continue;
				}

				$slideshowId = $screen->slideshow->id;
				$this->info("\n\nFound slideshowId {$slideshowId}\n");

				$slidesIds = $screen->slideshow->slides->pluck('id');
				$howManySlides = count($slidesIds);

				if ($howManySlides === 0 && !$this->confirm("Slideshow {$slideshowId} contains no slides. Is it ok?")) {
					$this->error("Screen {$screenId} not removed because it contains no slides.\n\n");
					continue;
				}

				$this->info("Ok, I'm about to remove screen {$screenId}, slideshow {$slideshowId} and {$howManySlides} slides. God have mercy on us.\n");

				Slide::whereIn('id', $slidesIds)->delete();
				Presentable::whereIn('slide_id', $slidesIds)->delete();
				Slideshow::find($slideshowId)->delete();
				$screenSections = Section::where('screen_id', $screenId);
				$subsections = Subsection::whereIn('section_id', $screenSections->pluck('id'));
				$screenSections->delete();
				$subsections->delete();
				\DB::table('taggables')->where([['taggable_id', $screenId], ['taggable_type', 'App\\Models\\Screen']])->delete();
				$screen->delete();

				$this->info("All removed.\n\n");
			}

			$bar->finish();

			$this->info("\n\nThe end! Now go, and see what you broke.\n");
		}
	}
}
