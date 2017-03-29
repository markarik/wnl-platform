<?php

namespace Tests\Browser\Payment;


use Faker\Factory;

trait SignsUpUsers
{
	/**
	 * Generates user data needed for filling in the sign-up form
	 *
	 * @param Factory $faker
	 * @return array
	 */
	protected function generateFormData(Factory $faker)
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
	 * Fills in sign-up form
	 *
	 * @param $data
	 * @param bool $invoice
	 */
	protected function fillInForm($data, $invoice = false)
	{

	}
}