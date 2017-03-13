<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Transformers\EditionTransformer;
use App\Models\Edition;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;

class EditionsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.editions');
	}

}
