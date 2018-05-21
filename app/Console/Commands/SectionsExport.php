<?php

namespace App\Console\Commands;

use App\Models\Presentable;
use App\Models\Screen;
use Illuminate\Console\Command;

class SectionsExport extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sections:export {screenId}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export the sections structure of a screen.';

	protected $screenId;

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
		$this->screenId = $this->argument('screenId');
		$screen = $this->getScreen();
		$slideshow = $this->getSlideshow($screen);
		$presentables = $this->getPresentables($slideshow);
		$out = [];

		foreach ($screen->sections->sortBy('order_number') as $section) {
			$sp = $presentables->whereIn('slide_id',
				$section->slides->pluck('id')->toArray()
			);
			$start = $sp->min('order_number');
			$end = $sp->max('order_number');
			array_push($out, "#{$section->name};{$start};{$end}");
			foreach ($section->subsections as $subsection) {
				$ssp = $presentables->whereIn('slide_id',
					$subsection->slides->pluck('id')->toArray()
				);
				$start = $ssp->min('order_number');
				$end = $ssp->max('order_number');
				array_push($out, "*{$subsection->name};{$start};{$end}");
			}
		}

		print implode("\n", $out) . "\n";

		return;
	}

	protected function getScreen()
	{
		$screen = Screen::with([
			'sections.subsections.slides',
			'sections.slides',
		])->find($this->screenId);

		if (!$screen) die("Screen with ID {$this->screenId} does not exist.\n");

		return $screen;
	}

	protected function getSlideshow($screen)
	{
		$slideshow = $screen->slideshow ?? null;

		if (!$slideshow) die("Can't find me a slideshow... :(");

		return $slideshow;
	}

	protected function getPresentables($slideshow)
	{
		$presentables = Presentable::select(['slide_id', 'order_number'])
			->where([
				['presentable_type', '=', 'App\\Models\\Slideshow'],
				['presentable_id', '=', $slideshow->id],
			])
			->get();

		return $presentables;
	}
}
