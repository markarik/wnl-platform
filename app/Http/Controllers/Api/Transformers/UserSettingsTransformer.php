<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\UserSettings;

class UserSettingsTransformer extends ApiTransformer
{
	public function transform(UserSettings $userSettings)
	{
		return $userSettings->settings;
	}
}