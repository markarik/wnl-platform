<?php namespace App\Http\Controllers\Api\Concerns;


use Illuminate\Http\Request;

trait PerformsApiSearches
{
	public function search(Request $request)
	{
		dd('Karmiński ty debilu', $request->route('resource'));

		// Does the resource exist ?

		// Is the resource searchable ?


	}
}
