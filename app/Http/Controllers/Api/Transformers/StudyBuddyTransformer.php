<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Payment;
use App\Http\Controllers\Api\ApiTransformer;
use App\Models\StudyBuddy;

class StudyBuddyTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parentData = [])
	{
		$this->parent = $parentData;
	}

	public function transform(StudyBuddy $studyBuddy)
	{
		$orderId = $this->parent['order_id'] ?? null;

		$data = [
			'id'        => $studyBuddy->id,
			'code'      => $studyBuddy->code,
			'recipient' => $studyBuddy->recipient,
			'refunded'  => $studyBuddy->refunded,
			'status'    => $studyBuddy->status,
			'orders'    => $orderId,
		];

		return $data;
	}
}
