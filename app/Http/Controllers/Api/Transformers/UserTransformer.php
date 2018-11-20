<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\User;
use App\Http\Controllers\Api\ApiTransformer;


class UserTransformer extends ApiTransformer
{
	protected $availableIncludes = [
		'roles',
		'profile',
		'subscription',
		'orders',
		'billing',
		'settings',
		'coupons',
		'userAddress',
	];

	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(User $user)
	{
		$data = [
			'id' => $user->id,
			'first_name' => $user->first_name,
			'last_name' => $user->last_name,
			'full_name' => $user->full_name,
			'created_at' => $user->created_at->timestamp ?? '',
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeRoles(User $profile)
	{
		$roles = $profile->roles;

		return $this->collection($roles, new RoleTransformer(['users' => $profile->id]), 'roles'	);
	}

	public function includeProfile(User $user)
	{
		$profile = $user->profile;

		return $this->item($profile, new UserProfileTransformer(['users' => $user->id]), 'profiles');
	}

	public function includeSubscription(User $user)
	{
		$subscription = $user->subscription;

		return $this->item($subscription, new UserSubscriptionTransformer(['users' => $user->id]), 'user_subscriptions');
	}

	public function includeOrders(User $user)
	{
		$orders = $user->orders;

		return $this->collection($orders, new OrderTransformer(['users' => $user->id]), 'orders');
	}

	public function includeBilling(User $user)
	{
		$billingData = $user->billing;

		return $this->item($billingData, new UserBillingTransformer(['user' => $user->id]), 'billings');
	}

	public function includeSettings(User $user)
	{
		$settings = $user->settings;

		return $this->item($settings, new UserSettingsTransformer(['user' => $user->id]), 'settings');
	}

	public function includeCoupons(User $user)
	{
		$coupons = $user->coupons;

		return $this->collection($coupons, new CouponsTransformer(['user' => $user->id]), 'coupons');
	}

	public function includeAddress(User $user)
	{
		$address = $user->userAddress;

		return $this->item($address, new UserAddressTransformer(['user' => $user->id]), 'addresses');
	}
}
