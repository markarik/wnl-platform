<?php

namespace Tests\Browser\Payment;


use Faker\Factory;

/**
 * Trait SignsUpUsers
 *
 * Used for interactions with sign-up form.
 * We don't want page methods, because we'll
 * need faker data later (on other pages) to make assertions
 * and this seems to be a cleaner solution in this case.
 *
 * @package Tests\Browser\Payment
 */
trait SignsUpUsers
{
	/**
	 * Generate user data needed for filling in the sign-up form
	 *
	 * @param Factory $faker
	 * @return array
	 */
	protected function generateFormData($faker)
	{
		$data = [
			'password'    => $faker->password,
			'email'       => $faker->email,
			'firstName'   => $faker->firstName,
			'lastName'    => $faker->lastName,
			'address'     => $faker->streetAddress,
			'phoneNumber' => $faker->phoneNumber,
			'postcode'    => $faker->postcode,
			'city'        => $faker->city,
		];

		return $data;
	}

	/**
	 * Fill in sign-up form
	 *
	 * @param $user
	 * @param $browser
	 * @param bool $invoice
	 */
	protected function fillInForm($user, $browser, $invoice = false)
	{
		$browser
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
			->pageDown();
	}
}