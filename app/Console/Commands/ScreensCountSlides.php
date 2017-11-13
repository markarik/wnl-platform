<?php

namespace App\Console\Commands;

use App\Models\Screen;
use App\Models\Section;
use App\Models\Subsection;
use Illuminate\Console\Command;

class ScreensCountSlides extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'screens:countSlides';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Count slides in every screen of type slideshow and save the number in screens meta';

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
		foreach (Screen::all() as $screen) {
			if (!empty($screen->slideshow)) {
				$slidesCount = \DB::table('presentables')
					->where('presentable_type', 'App\Models\Slideshow')
					->where('presentable_id', $screen->slideshow->id)
					->count();

				$screen->meta = array_merge($screen->meta, ['slides_count' => $slidesCount]);
				$screen->save();
			}
		}

		foreach (Section::all() as $section) {
			$sectionSlides = \DB::table('presentables')
				->select('order_number')
				->where('presentable_type', 'App\Models\Section')
				->where('presentable_id', $section->id)
				->orderBy('order_number')
				->get(['order_number']);

			$section->first_slide = $sectionSlides->first()->order_number;
			$section->slides_count = $sectionSlides->count();
			$section->save();
		}

		foreach (Subsection::all() as $subsection) {
			$subsectionSlides = \DB::table('presentables')
				->select('order_number')
				->where('presentable_type', 'App\Models\Subsection')
				->where('presentable_id', $subsection->id)
				->orderBy('order_number', 'asc')
				->orderBy('order_number')
				->get(['order_number']);

			\Log::debug('subsection id ' .  $subsection->id);
			\Log::debug('subsection name ' .  $subsection->name);
			\Log::debug('parent section name ' .  $subsection->section->name);
			$subsection->first_slide = $subsectionSlides->first()->order_number;
			$subsection->slides_count = $subsectionSlides->count();
			$subsection->save();
		}

		return;
	}
}
