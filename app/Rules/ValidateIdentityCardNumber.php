<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateIdentityCardNumber implements Rule
{
	const TOKEN_VALUE = [
		'0'=>0, '1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5, '6'=>6, '7'=>7, '8'=>8, '9'=>9,
		'A'=>10, 'B'=>11, 'C'=>12, 'D'=>13, 'E'=>14, 'F'=>15, 'G'=>16, 'H'=>17,
		'I'=>18, 'J'=>19, 'K'=>20, 'L'=>21, 'M'=>22, 'N'=>23, 'O'=>24, 'P'=>25,
		'Q'=>26, 'R'=>27, 'S'=>28, 'T'=>29,'U'=>30, 'V'=>31, 'W'=>32, 'X'=>33,
		'Y'=>34, 'Z'=>35
	];

	public function passes($attribute, $value)
	{
		$value = preg_replace("/[^A-Z0-9]*/", "", strtoupper($value));
		$weight = [7, 3, 1, 0, 7, 3, 1, 7, 3];
		$sum = 0;

		if (strlen($value) !== 9) return false;

		for ($i = 0; $i < 9; $i++) {
			if ($i < 3 && self::TOKEN_VALUE[$value[$i]] < 10){
				return false;
			} elseif ($i > 2 && self::TOKEN_VALUE[$value[$i]] > 9) {
				return false;
			}

			$sum += ((int)self::TOKEN_VALUE[$value[$i]]) * $weight[$i];
		}

		if ($sum % 10 != $value[3]) {
			return false;
		}

		return true;
	}

	public function message()
	{
		return 'Numer dowodu jest nieprawidłowy :(';
	}
}
