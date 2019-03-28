<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
	protected $fillable = [
		'settings',
	];

	protected $casts = [
		'settings' => 'array',
	];

	public function getSettingsAttribute()
	{
		$settings = json_decode($this->attributes['settings'], true);

		return array_merge(config('user-default-settings'), $settings);
	}

	public function patch($settings) {
		$this->settings = array_merge(json_decode($this->attributes['settings'], true), $settings);
		$this->save();
	}

}
