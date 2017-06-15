<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Reactable;
use App\Http\Controllers\Api\ApiTransformer;

class ReactableTransformer extends ApiTransformer
{
	public function transform(Reactable $reactable)
	{
		return [
			'id'             => $reactable->id,
			'user_id'        => $reactable->user_id,
			'reaction_id'    => $reactable->reaction_id,
			'reactable_id'   => $reactable->reactable_id,
			'reactable_type' => $reactable->reactable_type,
			'created_at'     => $reactable->created_at->timestamp,
			'updated_at'     => $reactable->updated_at->timestamp,
		];
	}
}
