<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\UserAddress;

class UserAddressTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserAddress $address)
	{
		$data = [
			'address' => $address->address,
			'zip'     => $address->zip,
			'city'    => $address->city,
			'phpne'   => $address->phone,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
