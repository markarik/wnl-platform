<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserBillingData;
use App\Models\UserProfile;
use App\Models\UserSettings;
use App\Policies\User\UserAddressPolicy;
use App\Policies\User\UserProfilePolicy;
use App\Policies\User\UserBillingPolicy;
use App\Policies\User\UserSettingsPolicy;
use App\Policies\User\UserPolicy;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		User::class            => UserPolicy::class,
		UserProfile::class     => UserProfilePolicy::class,
		UserAddress::class     => UserAddressPolicy::class,
		UserBillingData::class => UserBillingPolicy::class,
		UserSettings::class    => UserSettingsPolicy::class,
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerPolicies();
	}
}
