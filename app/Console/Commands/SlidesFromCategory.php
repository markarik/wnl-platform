<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Slide;
use App\Models\Lesson;

class SlidesFromCategory extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slides:fromCategory';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create Presentables for categories';

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
		$allCategories = Category::all();
		$bar = $this->output->createProgressBar(count($allCategories));

		foreach ($allCategories as $category) {
			$categoryTag = Tag::where('name', $category->name)->first();
			if (!empty($category->parent_id)) {
				$lessonsWithTag = Lesson::whereHas('tags', function($tag) use ($categoryTag) {
					$tag->where('name', $categoryTag->name);
				})->orderBy('order_number')->get();

				$orderNumber = 0;

				foreach($lessonsWithTag as $lesson) {
					foreach($lesson->screens as $screen) {
						if (!empty($screen->slideshow)) {
							$slidesIds = \DB::table('presentables')
								->where('presentable_type', 'App\Models\Slideshow')
								->where('presentable_id', $screen->slideshow->id)
								->orderBy('order_number')
								->get(['slide_id']);


							$slides = Slide::find($slidesIds->pluck('slide_id'));

							foreach($slides as $slide) {
								if (!$category->slides->contains($slide)) {
									$category->slides()->attach($slide, ['order_number' => $orderNumber]);
									$orderNumber++;
								}
							}
							break;
						}
					}
				}
			}
			$bar->advance();
		}

		print PHP_EOL;
	}
}
