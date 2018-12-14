<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\UserAddress;
use App\Http\Controllers\Api\ApiTransformer;

class UserAddressTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserAddress $address)
	{
		$data = [
			'id' => $address->id,
			'street' => $address->street,
			'zip' => $address->zip,
			'city' => $address->city,
			'phone' => $address->phone,
			'recipient' => $address->recipient,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
