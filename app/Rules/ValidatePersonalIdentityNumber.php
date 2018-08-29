<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidatePersonalIdentityNumber implements Rule
{
	public function passes($attribute, $value)
	{
		$sum = 0;

		if (!preg_match('/^[0-9]{11}$/', $value)) {
			return false;
		}

		$weight = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3, 1];

		for ($i = 0; $i < strlen($value); $i++) {
			$sum += $weight[$i] * $value[$i];
		}

		if (!($sum % 10 === 0)) {
			return false;
		}

		return true;
	}

	public function message()
	{
		return 'PESEL jest nieprawidłowy :(';
	}
}
