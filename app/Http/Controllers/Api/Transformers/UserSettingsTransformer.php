<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\UserSettings;

class UserSettingsTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserSettings $userSettings)
	{
		$data = array_merge($userSettings->settings_with_defaults, ['id' => $userSettings->id]);

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
