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
        $this->call(PermissionsSeeder::class);
        
        /** Course structure */
        $this->call(QuizSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(SlideshowSeeder::class);
        $this->call(CoursePlanSeeder::class);
        $this->call(ScreensTableSeeder::class);
        $this->call(ReactionsSeeder::class);
        $this->call(CategoriesTableSeeder::class);

        Cache::flush();
    }
}
