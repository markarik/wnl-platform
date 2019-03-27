<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\UserProductState;

class UserProductStateTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserProductState $userProductState)
	{
		$data = [
			'id' => $userProductState->id,
			'product_id' => $userProductState->product_id,
			'onboarding_step' => $userProductState->onboarding_step,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
