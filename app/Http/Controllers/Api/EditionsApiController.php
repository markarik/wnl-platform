<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Transformers\EditionTransformer;
use App\Models\Edition;
use League\Fractal\Resource\Item;

class EditionsApiController extends ApiController
{
	/**
	 * @param $editionId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function get($editionId)
	{
		$this->fractal->parseIncludes('groups.lessons');
		$edition = Edition::find($editionId);
		$resource = new Item($edition, new EditionTransformer, 'edition');

		$data = $this->fractal->createData($resource)->toArray();

		return response()->json($data);
	}

}
