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
	use SignsUpUsers;

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

			$user = $this->generateFormData($this->faker);

			$browser
				->on(new PersonalDataPage)
				->type('email', $user['email'])
				->type('password', $user['password'])
				->type('password_confirmation', $user['password'])
				->type('phone', $user['phoneNumber'])
				->pageDown()
				->type('first_name', $user['firstName'])
				->type('last_name', $user['lastName'])
				->type('address', $user['address'])
				->type('zip', $user['postcode'])
				->type('city', $user['city'])
				->check('consent_account')
				->check('consent_order')
				->check('consent_newsletter')
				->check('consent_terms')
				->pageDown()
				->xpath('.//button[@class="button is-primary"]')->click();

			$browser
				->on(new ConfirmOrderPage)
				->assertSeeAll([$user['email'], $user['firstName'], $user['lastName'], $user['address']])
				->xpath('.//button[1]')->click();

			$browser
				->on(new OrdersPage)
				->waitForAll(['Twoje zamówienia', $user['firstName'], $user['lastName']]);
		});
	}

	/** @test */
	public function user_can_successfully_place_order_using_online_payment()
	{
		if (env('APP_ENV') !== 'sandbox') {
			print PHP_EOL . 'Omitting test ' . __METHOD__ . ' (applicable only on sandbox)';

			return true;
		}

		$this->browse(function ($browser) {
			$browser
				->visit(new SelectProductPage)
				->clickLink('Wybieram kurs internetowy');


		});
	}
}
