<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\SlideshowBuilderApiController;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\Slide;
use App\Models\Tag;
use Cache;
use Illuminate\Console\Command;

class SlidesFromCategory extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slides:fromCategory {categoryId?}';

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
		$categoryId = $this->argument('categoryId');
		$category = Category::find($categoryId);
		if (!empty($category)) {
			$this->handleCategory($category);
		} else {
			$allCategories = Category::all();
			$bar = $this->output->createProgressBar(count($allCategories));

			foreach ($allCategories as $category) {
				$this->handleCategory($category);
				$bar->advance();
			}
		}


		print PHP_EOL;
	}

	private function handleCategory($category) {
		\DB::table('presentables')
			->where('presentable_type', 'App\Models\Category')
			->where('presentable_id', $category->id)
			->delete();

		$categoryTag = Tag::where('name', $category->name)->first();
		if (!empty($category->parent_id)) {
			$lessonsWithTag = Lesson::whereHas('tags', function($tag) use ($categoryTag) {
				$tag->where('name', $categoryTag->name);
			})->orderBy('order_number')->get();


			$this->attachLessonsSlidesToCategory($lessonsWithTag, $category);
		}
	}

	private function attachLessonsSlidesToCategory($lessonsWithTag, $category) {
		$orderNumber = 0;

		foreach($lessonsWithTag as $lesson) {
			$screens = $lesson->screens()->where('type', 'slideshow')->get();

			if ($screens->isEmpty()) {
				continue;
			}

			foreach ($screens as $screen) {
				$slidesIds = \DB::table('presentables')
					->where('presentable_type', 'App\Models\Slideshow')
					->where('presentable_id', $screen->slideshow->id)
					->orderBy('order_number')
					->get(['slide_id'])->pluck('slide_id');

				foreach($slidesIds as $slideId) {
					$slide = Slide::find($slideId);
					try {
						$category->slides()->attach($slide, ['order_number' => $orderNumber]);
						$orderNumber++;
					} catch (\Illuminate\Database\QueryException $e) {
						if ($e->errorInfo[1] === 1062) {
							// Means entry is duplicated.
						} else {
							throw $e;
						}
					}
				}
			}
		}

		$cacheKey = SlideshowBuilderApiController::key(
			sprintf(SlideshowBuilderApiController::CATEGORY_SUBKEY, $category->id)
		);

		Cache::forget($cacheKey);
	}
}
