<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Api\Concerns\GeneratesApiResponses;
use App\Http\Controllers\Api\Serializer\ApiJsonSerializer;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;

abstract class ApiController extends Controller
{
	use GeneratesApiResponses;

	protected $fractal;

	public function __construct()
	{
		$this->fractal = new Manager();
		$this->fractal->setRecursionLimit(5);
		$this->fractal->setSerializer(new ApiJsonSerializer());
	}
}
