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
//		$this->call(GroupsTableSeeder::class);
//		$this->call(LessonsTableSeeder::class);
//		$this->call(SectionsTableSeeder::class);

		$this->call(CategoriesTableSeeder::class);

//		$this->call(SlidesTableSeeder::class);
//		$this->call(PresentablesTableSeeder::class);

		$this->call(EditionsTableSeeder::class);
//		$this->call(LessonAvailabilitySeeder::class);
		$this->call(SubscribersTableSeeder::class);
		$this->call(CouponsTableSeeder::class);

		$this->call(TagsTableSeeder::class);
		$this->call(QnaQuestionsTableSeeder::class);
		$this->call(QnaAnswersTableSeeder::class);
		$this->call(TaggablesTableSeeder::class);

		$this->call(SlideshowSeeder::class);
		$this->call(ScreensTableSeeder::class);
		$this->call(QuizSeeder::class);
	}
}
