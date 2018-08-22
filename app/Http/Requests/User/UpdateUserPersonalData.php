<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPersonalData extends FormRequest
{
	const TOKEN_VALUE = [
		'0'=>0,'1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5,'6'=>6,'7'=>7,'8'=>8,'9'=>9,
		'A'=>10, 'B'=>11, 'C'=>12, 'D'=>13, 'E'=>14, 'F'=>15, 'G'=>16, 'H'=>17,
		'I'=>18, 'J'=>19, 'K'=>20, 'L'=>21, 'M'=>22, 'N'=>23, 'O'=>24, 'P'=>25,
		'Q'=>26, 'R'=>27, 'S'=>28, 'T'=>29,'U'=>30, 'V'=>31, 'W'=>32, 'X'=>33,
		'Y'=>34, 'Z'=>35
	];

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$user = User::fetch($this->route('userId'));

		return $this->user()->can('update', $user);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		if ($this->request->get('identity_type') == 'personal_identity_number') {
			return [
				'personal_identity_number' => 'required|digits:11'
			];
		} else if (
			$this->request->get('identity_type') == 'identity_card'
			|| $this->request->get('identity_type') == 'passport'
		) {
			return [
				'personal_identity_number' => 'required|string|size:9'
			];
		}
	}

	/**
	 * Configure the validator instance.
	 *
	 * @param  \Illuminate\Validation\Validator $validator
	 *
	 * @return void
	 */
	public function withValidator($validator)
	{
		$validator->after(function ($validator) {
			if (!$this->checksum()) {
				$validator->errors()->add('personal_identity_number', 'Numer jest nipoprawny!');
			}
		});
	}

	public function checksum()
	{
		$idNumber = $this->request->get('personal_identity_number');
		$idType = $this->request->get('identity_type');

		if ($idType == 'personal_identity_number') {
			$this->validatePersonalIdNumber($idNumber);
		} else if ($idType === 'identity_card') {
			$this->validateIdCardNumber($idNumber);
		} else if ($idType === 'passport') {
			$this->validatePassportNumber($idNumber);
		}

		return true;
	}

	public function validatePersonalIdNumber($idNumber)
	{
		$sum = 0;

		if (!preg_match('/^[0-9]{11}$/', $idNumber)) {
			return false;
		}

		$weight = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3, 1];

		for ($i = 0; $i < strlen($idNumber); $i++) {
			$sum += $weight[$i] * $idNumber[$i];
		}

		if (!($sum % 10 === 0)) {
			return false;
		}

		return true;
	}

	public function validateIdCardNumber($idNumber)
	{
		$idNumber = strtoupper($idNumber);
		$weight = [7, 3, 1, 0, 7, 3, 1, 7, 3];
		$sum = 0;

		for($i = 0; $i < 9; $i++){
			if($i < 3 && self::TOKEN_VALUE[$idNumber[$i]] < 10){
				return false;
			} elseif ($i > 2 && self::TOKEN_VALUE[$idNumber[$i]] > 9) {
				return false;
			}

			$sum += ((int)self::TOKEN_VALUE[$idNumber[$i]]) * $weight[$i];
		}

		if ($sum % 10 != $idNumber[3]) {
			return false;
		}

		return true;
	}

	public function validatePassportNumber($idNumber)
	{
		$idNumber = strtoupper($idNumber);
		$weight = [7, 3, 0, 1, 7, 3, 1, 7, 3];
		$sum = 0;

		for($i = 0; $i < 9; $i++){
			if($i < 2 && self::TOKEN_VALUE[$idNumber[$i]] < 10){
				return false;
			} elseif ($i > 1 && self::TOKEN_VALUE[$idNumber[$i]] > 9) {
				return false;
			}

			$sum += ((int)self::TOKEN_VALUE[$idNumber[$i]]) * $weight[$i];
		}

		if ($sum % 10 != $idNumber[2]) {
			return false;
		}

		return true;
	}
}
