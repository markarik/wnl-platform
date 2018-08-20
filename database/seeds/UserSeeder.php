<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (ExampleData::USERS as $user) {
            $pass = str_random();

            \DB::table('users')->insert([
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'phone' => encrypt('000000000'),
                'address' => encrypt(''),
                'password' => bcrypt($pass),
            ]);

            if (!empty($user['roles'])) {
                $this->assignRoles($user['roles'], $user['email']);
            }

            print "Password for {$user['email']} is {$pass}\n";
        }
    }

    private function assignRoles($roleNames, $email) {
        $roleEntries = [];
        foreach ($roleNames as $roleName) {
            $user = \DB::table('users')->select(['id'])->where('email', $email)->first();
            $role = \DB::table('roles')->select(['id'])->where('name', $roleName)->first();
            array_push($roleEntries, ['user_id' => $user->id, 'role_id' => $role->id]);
        }
        \DB::table('role_user')->insert($roleEntries);
    }
}
