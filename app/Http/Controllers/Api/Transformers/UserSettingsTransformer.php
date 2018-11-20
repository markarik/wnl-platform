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
		$data = $userSettings->settings;

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
