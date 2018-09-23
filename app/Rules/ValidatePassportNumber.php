<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidatePassportNumber implements Rule
{
	public function passes($attribute, $value)
	{
		// SINCE FOREIGN PASSPORTS HAVE MANY DIFFERENT VALIDATION
		// RULES, WE CANNOT USE ONLY ONE. HENCE THIS NAIVE SOLUTION.
		$illegalChars = [];
		preg_match('/[^A-Z0-9]/', strtoupper($value), $illegalChars);
		return count($illegalChars) === 0;
	}

	public function message()
	{
		return 'Numer paszportu jest nieprawidłowy :(';
	}
}
