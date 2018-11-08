<?php

use App\Models\Role;
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
        $admins = \App\Models\User::ofRole(Role::ROLE_ADMIN);

        $aFewDaysAgo = \Carbon\Carbon::now()->subDays(5);
        $someday = \Carbon\Carbon::now()->addYears(5);

        foreach ($admins as $admin) {
            DB::table('user_subscription')->insert([
                'user_id' => $admin->id,
                'access_start' => $aFewDaysAgo,
                'access_end' => $someday,
            ]);
        }
    }
}
