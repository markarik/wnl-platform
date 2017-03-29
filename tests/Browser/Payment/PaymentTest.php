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

			$browser
				->visit(new SelectProductPage)
				->clickLink('Wybieram kurs stacjonarny');

			$password = $this->faker->password;
			$email = $this->faker->email;
			$firstName = $this->faker->firstName;
			$lastName = $this->faker->lastName;
			$address = $this->faker->streetAddress;

			$browser
				->on(new PersonalDataPage)
				->type('email', $email)
				->type('password', $password)
				->type('password_confirmation', $password)
				->type('phone', $this->faker->phoneNumber)
				->pageDown()
				->type('first_name', $firstName)
				->type('last_name', $lastName)
				->type('address', $address)
				->type('zip', $this->faker->postcode)
				->type('city', $this->faker->city)
				->check('consent_account')
				->check('consent_order')
				->check('consent_newsletter')
				->check('consent_terms')
				->pageDown()
				->xpath('.//button[@class="button is-primary"]')->click();

			$browser
				->on(new ConfirmOrderPage)
				->assertSeeAll([$email, $firstName, $lastName, $address])
				->xpath('.//button[1]')->click();

			$browser
				->on(new OrdersPage)
				->waitForAll(['Twoje zamówienia', $firstName, $lastName]);
		});
	}
}
