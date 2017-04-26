<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserBilling;
use App\Models\UserProfile;
use App\Models\UserSettings;
use App\Policies\User\UserAddressPolicy;
use App\Policies\User\UserProfilePolicy;
use App\Policies\User\UserUserBillingPolicy;
use App\Policies\User\UserUserSettingsPolicy;
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
		User::class         => UserPolicy::class,
		UserProfile::class  => UserProfilePolicy::class,
		UserAddress::class  => UserAddressPolicy::class,
		UserBilling::class  => UserUserBillingPolicy::class,
		UserSettings::class => UserUserSettingsPolicy::class,
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
