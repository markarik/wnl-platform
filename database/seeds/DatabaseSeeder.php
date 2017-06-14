<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call(RolesTableSeeder::class);
		$this->call(UsersTableSeeder::class);
		$this->call(UserProfilesTableSeeder::class);
		$this->call(ProductsTableSeeder::class);
		$this->call(CoursesTableSeeder::class);
		$this->call(CategoriesTableSeeder::class);
		$this->call(EditionsTableSeeder::class);
//		$this->call(GroupsTableSeeder::class);
//		$this->call(LessonsTableSeeder::class);
		$this->call(SubscribersTableSeeder::class);
		$this->call(CouponsTableSeeder::class);
		$this->call(TagsTableSeeder::class);
		$this->call(QnaQuestionsTableSeeder::class);
		$this->call(QnaAnswersTableSeeder::class);
		$this->call(TaggablesTableSeeder::class);
//		$this->call(ScreensTableSeeder::class);
		$this->call(CommentsSeeder::class);
		$this->call(ReactionsSeeder::class);
		$this->call(SlideshowSeeder::class);
		$this->call(ChatRoomsSeeder::class);
		$this->call(QuizSeeder::class);
//		$this->call(ScreensTableSeeder::class);
		Cache::flush();
	}

	/**
	 * Get contents of a seeder source file.
	 *
	 * @param $path
	 *
	 * @return string
	 */
	public static function file($path)
	{
		if (Storage::exists($path)) {
			return Storage::get($path);
		}

		$fileContents = Storage::disk('s3')->get($path);
		Storage::put($path, $fileContents);

		return $fileContents;
	}
}
