<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Http\Controllers\Api\ApiTransformer;
use App\Models\PaymentMethod;
use Carbon\Carbon;

class PaymentMethodTransformer extends ApiTransformer
{
	private $parent;

	public function __construct($parent = [])
	{
		$this->parent = $parent;
	}

	public function transform(PaymentMethod $method)
	{
		$data = [
			'id'         => $method->id,
			'slug'       => $method->slug,
			'updated_at' => $method->updated_at->timestamp ?? null,
			'created_at' => $method->created_at->timestamp ?? null,
		];

		if ($this->parent) {
			$startDate = $method->pivot->getOriginal('start_date');
			$endDate = $method->pivot->getOriginal('end_date');
			$pivotData = [
				'start_date' => $startDate ? Carbon::parse($startDate)->timestamp : null,
				'end_date'   => $endDate ? Carbon::parse($endDate)->timestamp : null,
			];
			$data = array_merge($data, $this->parent, $pivotData);
		}

		return $data;
	}
}
