<?php

namespace App\Console\Commands;

use App\Models\Presentable;
use App\Models\Screen;
use App\Models\Section;
use App\Models\Slide;
use App\Models\Subsection;
use Illuminate\Console\Command;

class SlideshowRecalculateOrderNumber extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slideshow:recalc {--G|group=} {--S|screen=}';

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
			$screenPresentables = \DB::table('presentables')
				->where('presentable_type', 'App\Models\Slideshow')
				->where('presentable_id', $screen->slideshow->id)
				->orderBy('order_number')
				->get();

			$i = 0;
			foreach ($screenPresentables as $presentable) {
				// $this->info($presentable->id . ' ' . $presentable->order_number);
				$presentableObject = Presentable::find($presentable->id);
				$presentableObject->order_number = $i;
				$presentableObject->save();
				$i++;
			}
		}

		$this->info('OK.');

		return;
	}
}
