<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Slide;

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

		foreach ($allCategories as $category) {
			$categoryTag = Tag::where('name', $category->name)->first();
			if (!empty($category->parent_id)) {
				$slidesWithTag = Slide::whereHas('tags', function($tag) use ($categoryTag) {
					$tag->where('name', $categoryTag->name);
				})->get();

				$order_number = 0;
				foreach($slidesWithTag as $slide) {
					$category->slides()->attach($slide, ['order_number' => $order_number]);
					$order_number++;
				}
			}
		}
	}
}
