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
            $parent = Category::firstOrCreate(['name' => $group->name]);
            foreach ($group->lessons as $lesson) {
                \DB::table('categories')->insert([
                    'name' => $lesson->name,
                    'parent_id' => $parent->id
                ]);
            }
	    }

	    \Illuminate\Support\Facades\Artisan::call('tags:fromCategories');
	    \Illuminate\Support\Facades\Artisan::call('slides:fromCategory');
	}
}
