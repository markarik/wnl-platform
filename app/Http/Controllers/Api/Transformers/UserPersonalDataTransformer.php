<?php


namespace App\Http\Controllers\Api\Transformers;

use App\Models\User;
use App\Models\UserPersonalData;
use App\Http\Controllers\Api\ApiTransformer;

class UserPersonalDataTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserPersonalData $personalData)
	{
		$data = [
			'personalIdentityNumber' => $personalData->personal_identity_number,
			'identityCardNumber'     => $personalData->identity_card_number,
			'passportNumber'         => $personalData->passport_number
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
