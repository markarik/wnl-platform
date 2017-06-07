<?php namespace App\Http\Controllers\Api;


use League\Fractal\TransformerAbstract;
use App\Http\Controllers\Api\Concerns\IncludesResources;

abstract class ApiTransformer extends TransformerAbstract
{
	use IncludesResources;
}
