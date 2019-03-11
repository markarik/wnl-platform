<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
	    $groups = App\Models\Group::all();

        foreach ($groups as $group) {
            Category::firstOrCreate(['name' => $group->name]);
	    }

	    \Illuminate\Support\Facades\Artisan::call('tags:fromCategories');
	    \Illuminate\Support\Facades\Artisan::call('slides:fromCategory');
	}
}
