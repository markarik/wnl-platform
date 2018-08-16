<?php

use Illuminate\Database\Seeder;

class UserSubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_subscription')->insert([
            ['user_id' => 1, 'access_start' => Carbon::now()->subDays(5), 'access_end' => Carbon::now()->addDays(5)],
            ['user_id' => 2, 'access_start' => Carbon::now()->subDays(5), 'access_end' => Carbon::now()->addDays(5)],
            ['user_id' => 3, 'access_start' => Carbon::now()->subDays(5), 'access_end' => Carbon::now()->addDays(5)],
        ]);
    }
}
