<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Every command needed to set up the database
     * for a new platform instance.
     *
     * @return void
     */
    public function run()
    {
        /** User accounts & checkout */
        $this->call(RolesTableSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(UserProfilesTableSeeder::class);
        $this->call(UserSubscriptionSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(PaymentMethodSeeder::class);

        /** Learning features */

        /** Course structure */
        $this->call(CourseSeeder::class);
        $this->call(EditionSeeder::class);
        $this->call(SlideshowSeeder::class);
        $this->call(CoursePlanSeeder::class);
//        $this->call(GroupsTableSeeder::class);
//        $this->call(LessonsTableSeeder::class);
        $this->call(ScreensTableSeeder::class);

        /** Social features */
//        $this->call(CategoriesTableSeeder::class);
//        $this->call(TagsTableSeeder::class);
//        $this->call(QnaQuestionsTableSeeder::class);
//        $this->call(QnaAnswersTableSeeder::class);
//        $this->call(TaggablesTableSeeder::class);
//        $this->call(CommentsSeeder::class);

//        $this->call(ReactionsSeeder::class);
//        $this->call(QuizSeeder::class);
        Cache::flush();
    }
}
