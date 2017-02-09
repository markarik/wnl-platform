<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory;

class PaymentStepTwoTest extends TestCase
{
	/**
	 * @var Factory
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

	/**
	 * My test implementation
	 */
	public function test_user_can_sign_up()
	{
		$this->visit('/payment/step2');
		$this->type('Jeff', 'first_name');
		$this->type('Healey', 'last_name');
		$this->type($this->faker->email, 'email');
		$this->type('Asnyka', 'address');
		$this->type('61-131', 'zip');
		$this->type('Poznań', 'city');
		$this->type('123456', 'password');
		$this->type('123456', 'password_confirmation');
		$this->check('privacy_policy');
		$this->check('newsletter');
		$this->press('Dalej');
		$this->see('Twoje dane:');
	}
}
