<?php

namespace Tests\Browser\Payment;

use Facebook\WebDriver\WebDriverPoint;
use Faker\Factory;
use Tests\Browser\Pages\Payment\ConfirmOrderPage;
use Tests\Browser\Pages\Payment\PersonalDataPage;
use Tests\Browser\Pages\Payment\SelectProductPage;
use Tests\Browser\Pages\User\OrdersPage;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PaymentTest extends DuskTestCase
{
	/**
	 * @var (Faker) Factory
	 */
	protected $faker;

	/**
	 * Setup faker
	 */
	public function setUp()
	{
		parent::setUp();
		$this->faker = Factory::create();
	}

	/** @test */
	public function user_can_sign_up_and_place_order()
	{
		$this->browse(function ($browser) {
			$browser->visit(new SelectProductPage);

			$browser->clickLink('Wybieram kurs stacjonarny');
			$browser->on(new PersonalDataPage);

			$password = $this->faker->password;
			$email = $this->faker->email;
			$firstName = $this->faker->firstName;
			$lastName = $this->faker->lastName;
			$address = $this->faker->streetAddress;

			$browser->type('email', $email);
			$browser->type('password', $password);
			$browser->type('password_confirmation', $password);
			$browser->type('phone', $this->faker->phoneNumber);
			$browser->pageDown();
			$browser->type('first_name', $firstName);
			$browser->type('last_name', $lastName);
			$browser->type('address', $address);
			$browser->type('zip', $this->faker->postcode);
			$browser->type('city', $this->faker->city);
			$browser->check('consent_account');
			$browser->check('consent_order');
			$browser->check('consent_newsletter');
			$browser->check('consent_terms');
			$browser->pageDown();

			$browser->xpath('.//button[@class="button is-primary"]')->click();
			$browser->on(new ConfirmOrderPage);
			$browser->assertSeeAll([$email, $firstName, $lastName, $address]);

			$browser->xpath('.//button[1]')->click();

			$browser->on(new OrdersPage);
			$browser->assertSeeAll([$firstName, $lastName]);
		});
	}
}
