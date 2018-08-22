<?php


namespace App\Http\Controllers\Api\Transformers;

use App\Models\User;
use App\Models\UserPersonalData;
use App\Http\Controllers\Api\ApiTransformer;

class UserPersonalDataTransformer extends ApiTransformer
{
    public function transform(UserPersonalData $personalData)
    {
        $data = [
            'identity' => [
                'personal_identity_number' => $user->personal_identity_number,
                'identity_type'            => $user->identity_type
            ],
        ];
    }
}
