<?php

namespace Tests\Browser\Tests\Payment;


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
	 *
	 * @return array
	 */
	protected function generateFormData($faker)
	{
		$data = [
			'password'         => $faker->password,
			'email'            => str_random() . '@bethink.pl',
			'firstName'        => $faker->firstName,
			'lastName'         => $faker->lastName,
			'address'          => $faker->streetAddress,
			'phoneNumber'      => $faker->phoneNumber,
			'postcode'         => $faker->postcode,
			'city'             => $faker->city,
			'invoice_company'  => $faker->company,
			'invoice_nip'      => $faker->randomNumber(9),
			'invoice_address'  => $faker->streetAddress,
			'invoice_postcode' => $faker->postcode,
			'invoice_city'     => $faker->city,
			'invoice_country'  => $faker->country,
		];

		return $data;
	}

	/**
	 * Fill in sign-up form
	 *
	 * @param $user
	 * @param $browser
	 * @param bool $invoice
	 * @param bool $password
	 */
	protected function fillInForm($user, $browser, $invoice = false, $password = true)
	{
		$browser->type('email', $user['email']);
		if ($password) {
			$browser
				->type('password', $user['password'])
				->type('password_confirmation', $user['password']);
		}
		$browser->type('phone', $user['phoneNumber'])
			->type('first_name', $user['firstName'])
			->type('last_name', $user['lastName'])
			->type('address', $user['address'])
			->type('zip', $user['postcode'])
			->type('city', $user['city']);

		if ($invoice) {
			$browser
				->check('invoice')
				->type('invoice_name', $user['invoice_company'])
				->type('invoice_nip', $user['invoice_nip'])
				->type('invoice_address', $user['invoice_address'])
				->type('invoice_zip', $user['invoice_postcode'])
				->type('invoice_city', $user['invoice_city'])
				->type('invoice_country', $user['invoice_country']);
		}

//		$browser->check('consent_account');
//		$browser->check('consent_order');
		$browser->check('consent_newsletter');
		$browser->check('consent_terms');
	}
}
