<?php namespace App\Http\Controllers\Api\Concerns;


use Illuminate\Http\Request;

trait PerformsApiSearches
{
	public function search(Request $request)
	{
		$query = $request->q;
		$resource = $request->route('resource');
		$model = static::getResourceModel($resource);

		// Does the resource exist ?

		// Is the resource searchable ?

		$raw = $model::search($query)->raw();

		return $this->respondOk($raw);
	}
}
