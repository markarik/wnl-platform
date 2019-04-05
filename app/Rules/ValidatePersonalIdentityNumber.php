<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class ValidatePersonalIdentityNumber implements Rule
{
	public function message()
	{
		return 'PESEL jest nieprawidłowy :(';
	}

	public function passes($attribute, $value)
	{
		if (!$this->validateChecksum($value)) return false;

		$birthDate = $this->getBirthDate($value);

		if (!checkdate($birthDate['month'], $birthDate['day'], $birthDate['year'])) return false;

		try {
			$parsedDate = Carbon::create($birthDate['year'], $birthDate['month'], $birthDate['day']);
		} catch (\Exception $e) {
			return false;
		}
		if ($parsedDate->isFuture()) return false;

		return true;
	}

	private function validateChecksum($value) {
		$sum = 0;
		$weight = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3, 1];

		for ($i = 0; $i < strlen($value); $i++) {
			$sum += $weight[$i] * $value[$i];
		}

		if (!($sum % 10 === 0)) {
			return false;
		}

		return true;
	}

	private function getBirthDate($value) {
		$monthInCentury = substr($value,2,2);
		$monthInCenturyFirstDigit = (int) substr($monthInCentury, 0, 1);
		$monthInCentury = (int) $monthInCentury;

		$century = 1900;
		$month = $monthInCentury;
		$day = (int) substr($value,4,2);

		if ($monthInCenturyFirstDigit === 2 || $monthInCenturyFirstDigit === 3) {
			$century = 2000;
			$month = $monthInCentury - 20;
		} else if ($monthInCenturyFirstDigit === 4 || $monthInCenturyFirstDigit === 5) {
			$century = 2100;
			$month = $monthInCentury - 40;
		} else if ($monthInCenturyFirstDigit === 6 || $monthInCenturyFirstDigit === 7) {
			$century = 2200;
			$month = $monthInCentury - 60;
		} else if ($monthInCenturyFirstDigit === 8 || $monthInCenturyFirstDigit === 9) {
			$century = 1800;
			$month = $monthInCentury - 80;
		}

		$year = $century + substr($value,0,2);

		return [
			'year' => $year,
			'month' => $month,
			'day' => $day
		];
	}
}
