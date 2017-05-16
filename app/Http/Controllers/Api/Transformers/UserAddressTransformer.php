<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\UserAddress;
use League\Fractal\TransformerAbstract;

class UserAddressTransformer extends TransformerAbstract
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserAddress $address)
	{
		$data = [
			'street' => $address->street,
			'zip'    => $address->zip,
			'city'   => $address->city,
			'phone'  => $address->phone,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
