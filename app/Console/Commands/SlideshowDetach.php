<?php

namespace App\Console\Commands;

use App\Models\Presentable;
use App\Models\Screen;
use App\Models\Section;
use App\Models\Slide;
use App\Models\Subsection;
use Illuminate\Console\Command;

class SlideshowDetach extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slideshow:detach {--G|group=} {--S|screen=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete slideshow and detach all of its slides in presentables table';

	/**
	 * Create a new command instance.
	 *
	 */

	const CACHE_BUSTER_REGEX = '/(\?cb=[^"]*)/';

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
		$group = $this->option('group');
		$screen = $this->option('screen');

		if ($group) {
			$screens = Screen::select()
				->whereHas('lesson', function ($query) use ($group) {
					$query->whereHas('group', function ($query) use ($group) {
						$query->where('id', $group);
					});
				})
				->where('type', 'slideshow')
				->get();
		} elseif ($screen) {
			$screens = Screen::whereIn('id', explode(',', $screen))
				->where('type', 'slideshow')
				->get();
			if ($screens->count() === 0) {
				$this->error('There is no screen of type slideshow having id: ' . $screen);
				exit;
			}
		} else {
			$this->error('Provide either --group or --screen option to continue.');
			exit;
		}

		foreach ($screens as $screen) {
			$slideshow = $screen->slideshow;
			$slides = $slideshow->slides;
			$this->removeCacheBusters($slides);
			$slideIds = $slides->pluck('id')->toArray();
			$sectionsIds = $screen->sections->pluck('id')->toArray();
			Presentable::whereIn('slide_id', $slideIds)->delete();
			Section::whereIn('id', $sectionsIds)->delete();
			Subsection::whereIn('section_id', $sectionsIds)->delete();
			$slideshow->delete();
			$screen->delete();
			print '.';
		}

		$this->info('OK.');

		return;
	}

	protected function removeCacheBusters($slides)
	{
		Slide::flushEventListeners();
		foreach ($slides as $slide) {
			$slide->update([
				'content' => preg_replace(self::CACHE_BUSTER_REGEX, '', $slide->content),
			]);
			print '-';
		}
	}
}
