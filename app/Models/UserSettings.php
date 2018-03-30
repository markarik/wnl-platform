<?php

namespace App\Models;

use App\Events\Users\UserDataUpdated;
use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
	protected $dispatchesEvents = [
		'updated' => UserDataUpdated::class,
	];

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

}
