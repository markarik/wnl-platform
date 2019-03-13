<?php

namespace Tests\Browser\Tests\Payment;


use Faker\Factory;
use Faker\Generator;
use Faker\Provider\pl_PL\Company;
use Faker\Provider\pl_PL\PhoneNumber;
use Faker\Provider\pl_PL\Address;
use Faker\Provider\Internet;
use Faker\Provider\pl_PL\Person;

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
	 * Generate user account data needed for filling in the sign-up form
	 *
	 * @param Factory $faker
	 *
	 * @return array
	 */
	protected function generateAccountFormData()
	{
		$faker = new Generator();
		$faker->addProvider(new Internet($faker));

		$data = [
			'password' => $faker->password,
			'email'    => str_random() . '@bethink.pl',
		];

		return $data;
	}

	/**
	 * Generate user personal data needed for filling in the sign-up form
	 *
	 * @param Factory $faker
	 *
	 * @return array
	 */
	protected function generatePersonalFormData()
	{
		$faker = new Generator();
		$faker->addProvider(new Person($faker));
		$faker->addProvider(new Address($faker));
		$faker->addProvider(new Internet($faker));
		$faker->addProvider(new PhoneNumber($faker));
		$faker->addProvider(new Company($faker));

		$data = [
			'firstName'        => $faker->firstName,
			'lastName'         => $faker->lastName,
			'address'          => $faker->streetAddress,
			'phoneNumber'      => $faker->phoneNumber,
			'postcode'         => $faker->postcode,
			'city'             => $faker->city,
			'invoice_company'  => $faker->company,
			'identity_number'  => $faker->pesel,
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
	protected function fillInAccountForm($user, $browser)
	{
		$browser->type('email', $user['email'])
			->type('password', $user['password']);
	}

	/**
	 * Fill in personal-data form
	 *
	 * @param $user
	 * @param $browser
	 * @param bool $invoice
	 * @param bool $password
	 */
	protected function fillInPersonalDataForm($user, $browser, $invoice = false)
	{
		$browser->type('phone', $user['phoneNumber'])
			->type('first_name', $user['firstName'])
			->type('last_name', $user['lastName'])
			->type('address', $user['address'])
			->type('zip', $user['postcode'])
			->type('city', $user['city']);

		$browser->type('identity_number', $user['identity_number']);

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
	}
}
