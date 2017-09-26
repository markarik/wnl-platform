<?php


namespace Tests\Browser\Tests\Payment\Modules;


use Faker\Generator;
use Faker\Provider\pl_PL\Person;
use Tests\Browser\Pages\Login;

class UserModule
{
	public function newUser($browser)
	{
		return [
			SelectProductModule::class,
			VoucherModule::class,
			PersonalDataModule::class,
		];
	}

	public function existingUser($browser)
	{
		$faker = new Generator();
		$faker->addProvider(new Person($faker));
		$user = factory(\App\Models\User::class)->create();
		$browser->user = $user;

		$browser
			->visit(new Login())
			->loginAsUser($user->email, 'secret');
//			->on(new Navigation())
//			->assertUserLoggedIn($user->first_name);

		return [
			SelectProductModule::class,
			VoucherModule::class,
			PersonalDataModule::class,
		];
	}
}
