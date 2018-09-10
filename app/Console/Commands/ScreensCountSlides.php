<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\EditionsApiController;
use App\Models\Screen;
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
		$screens = Screen::where('type', 'slideshow')->get();
		foreach ($screens as $screen) {
			if (empty($screen->slideshow)) {
				\Log::error("This is weird, screen {$screen->id} of type " .
					"slideshow has not slideshow id in its meta o_O");
				continue;
			}

			$screenPresentables = \DB::table('presentables')
				->where('presentable_type', 'App\Models\Slideshow')
				->where('presentable_id', $screen->slideshow->id)
				->get();

			$screen->meta = array_merge($screen->meta, [
				'slides_count' => $screenPresentables->count(),
			]);
			$screen->save();

			$this->countForSections($screen, $screenPresentables);
		}

		// TODO: https://bethink.atlassian.net/browse/PLAT-506 !!
		\Cache::tags('editions')->flush();

		return;
	}

	protected function countForSections($screen, $screenPresentables)
	{
		foreach ($screen->sections as $section) {
			$sectionSlides = \DB::table('presentables')
				->select('slide_id')
				->where('presentable_type', 'App\Models\Section')
				->where('presentable_id', $section->id)
				->orderBy('order_number')
				->get(['slide_id']);

			$firstSlideId = $sectionSlides->first()->slide_id;

			try {
				$orderNo = $screenPresentables
					->where('slide_id', $firstSlideId)
					->first()
					->order_number;
			}
			catch (\Exception $e) {
				\Log::error("Whooops, section {$section->id} has slide" .
					"{$firstSlideId}, but it is not present in the section's" .
					" parent slideshow... that may be an issue.");
				continue;
			}

			$section->first_slide = $orderNo;
			$section->slides_count = $sectionSlides->count();
			$section->save();

			$this->countForSubsections($section, $screenPresentables);
		}
	}

	protected function countForSubsections($section, $screenPresentables)
	{
		foreach ($section->subsections as $subsection) {
			$subsectionSlides = \DB::table('presentables')
				->select('slide_id')
				->where('presentable_type', 'App\Models\Subsection')
				->where('presentable_id', $subsection->id)
				->orderBy('order_number', 'asc')
				->orderBy('order_number')
				->get(['slide_id']);

			$firstSlideId = $subsectionSlides->first()->slide_id;
			try {
				$orderNo = $screenPresentables
					->where('slide_id', $firstSlideId)
					->first()
					->order_number;
			}
			catch (\Exception $e) {
				\Log::error("Whooops, subsection {$subsection->id} has slide" .
					"{$firstSlideId}, but it is not present in the section's" .
					" parent slideshow... that may be an issue.");
				continue;
			}

			$subsection->first_slide = $orderNo;
			$subsection->slides_count = $subsectionSlides->count();
			$subsection->save();
		}
	}
}
