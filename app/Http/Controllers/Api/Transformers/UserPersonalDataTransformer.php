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
            'identity' => [
                'personalIdentityNumber' => $personalData->personal_identity_number,
                'identityType'           => $personalData->identity_type
            ],
        ];

        if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
    }
}
