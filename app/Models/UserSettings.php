<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
	const SETTING_SKIP_SATISFACTION_GUARANTEE_MODAL = 'skip_satisfaction_guarantee_modal';

	protected $fillable = [
		'settings',
	];

	protected $casts = [
		'settings' => 'array',
	];

	public function getSettingsAttribute()
	{
		return json_decode($this->attributes['settings'], true);
	}

	public function getSettingsWithDefaultsAttribute()
	{
		return array_merge(config('user-default-settings'), $this->settings);
	}
}
