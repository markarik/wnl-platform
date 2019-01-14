<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UserCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $userInput = [
            'first_name' => $this->ask('First name'),
            'last_name' => $this->ask('Last name'),
            'email' => $this->ask('email'),
            'street' => $this->ask('Address (street)'),
            'zip' => $this->ask('Address (zip)'),
            'city' => $this->ask('Address (city)'),
            'phone' => $this->ask('Phone number', 000),
        ];

        $pass = $this->ask('Password', str_random(8));

        $user = User::create(
            [
                'first_name' => $userInput['first_name'],
                'last_name' => $userInput['last_name'],
                'email' => $userInput['email'],
                'password' => bcrypt($pass),
                'invoice' => 0,
                'consent_newsletter' => 0,
                'consent_account' => 1,
                'consent_order' => 1,
                'consent_terms' => 1,
            ]
        );

        $user->userAddress()->firstOrCreate([
            'street' => $userInput['street'],
            'zip' => $userInput['zip'],
            'city' => $userInput['city'],
            'phone' => $userInput['phone'],
        ]);

        $this->info(
            "User created! (ID {$user->id})\n" .
            "Now go and using account panel correct whatever you mistyped.\n" .
            "Login: {$user->email}\n" .
            "Password: {$pass}\n\n" .
            "To assign role use php artisan role:assign {roleName} {$user->id}\n" .
            "To activate user's subscription use php artisan order:create {$user->id}"
        );

        return;
    }
}
