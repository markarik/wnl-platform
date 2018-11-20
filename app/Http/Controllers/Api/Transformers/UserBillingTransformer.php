<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\UserBillingData;
use App\Http\Controllers\Api\ApiTransformer;

class UserBillingTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserBillingData $billingData)
	{
		$data = [
			'company_name' => $billingData->company_name,
			'vat_id' => $billingData->vat_id,
			'address' => $billingData->address,
			'zip' => $billingData->zip,
			'city' => $billingData->city,
			'country' => $billingData->country,
			'user_id' => $billingData->user_id,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
