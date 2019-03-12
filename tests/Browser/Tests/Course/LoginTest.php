<?php

namespace Tests\Browser;

use App\Models\User;
use Faker\Generator;
use Faker\Provider\Internet;
use Faker\Provider\Person;
use Tests\Browser\Pages\Course\Components\Navigation;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LoginTest extends DuskTestCase
{

	private $user;
	private $password;
	private $email;
	private $userName;

	public function setUp(): void
	{
		parent::setUp();

		$faker = new Generator();
		$faker->addProvider(new Person($faker));
		$faker->addProvider(new Internet($faker));

		$this->password = $faker->password;
		$this->email = $faker->unique()->safeEmail;
		$firstName = $faker->firstName;
		$lastName = $faker->lastName;
		$this->userName = sprintf('%s %s', $firstName, $lastName);

		$this->user = factory(User::class)->create(
			[
				'password' => bcrypt($this->password),
				'email' => $this->email,
				'first_name' => $firstName,
				'last_name' => $lastName
			]
		);
	}

	public function testLogin()
	{
		$this->browse(function (Browser $browser) {

			$browser
				->visit(new Login())
				->loginAsUser($this->email, $this->password)
				->on(new Navigation())
				->assertUserLoggedIn($this->userName);
		});
	}

	public function tearDown()
	{
		parent::tearDown();
		$this->user->delete();
	}
}
