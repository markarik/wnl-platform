<?php


namespace Tests\Browser\Tests\Payment\Modules;


use App\Models\Order;
use App\Models\User;
use Faker\Generator;
use Faker\Provider\Internet;
use Faker\Provider\pl_PL\Address;
use Faker\Provider\pl_PL\Person;
use Faker\Provider\pl_PL\PhoneNumber;
use Tests\BethinkBrowser;
use Tests\Browser\Pages\Login;

class UserModule
{
	public function existingUser(BethinkBrowser $browser)
	{
		$user = $this->createUser();
		$browser->user = $user;
		$browser->accountData = $user;

		$browser
			->visit(new Login())
			->loginAsUser($user->email, 'secret');
	}

	public function existingUserWithOrder(BethinkBrowser $browser)
	{
		$user = $this->createUser();
		$order = new Order([
			'user_id' => $user->id,
			'product_id' => 1,
		]);
		$order->paid = true;
		$order->save();

		$browser->user = $user;
		$browser->accountData = $user;

		$browser
			->visit(new Login())
			->loginAsUser($user->email, 'secret');
	}

	protected function createUser()
	{
		$faker = new Generator();
		$faker->addProvider(new Person($faker));
		$faker->addProvider(new Address($faker));
		$faker->addProvider(new Internet($faker));
		$faker->addProvider(new PhoneNumber($faker));
		$user = User::createWithProfileAndBilling([
			'first_name' => $faker->firstName,
			'last_name'  => $faker->lastName,
			'email'      => $faker->unique()->safeEmail,
			'password'   => bcrypt('secret'),
		]);
		$user->userAddress()->firstOrCreate([
			'phone'      => $faker->phoneNumber,
			'street'     => $faker->address,
			'zip'        => $faker->postcode,
			'city'       => $faker->city,
		]);

		$user->personalData()->firstOrCreate([
			'personal_identity_number' => $faker->pesel,
		]);

		return $user;
	}
}
