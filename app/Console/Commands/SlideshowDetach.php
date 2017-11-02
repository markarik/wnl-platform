<?php

namespace App\Console\Commands;

use App\Models\Screen;
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
		}

		elseif ($screen) {
			$screens = Screen::where('id', $screen)
				->where('type', 'slideshow')
				->get();
			if ($screens->count() === 0) {
				$this->error('There is no screen of type slideshow having id: ' . $screen);
				exit;
			}
		}

		else {
			$this->error('Provide either --group or --screen option to continue.');
			exit;
		}

		foreach ($screens as $screen) {
			$slideshow = '';
		}

		return;
	}
}
