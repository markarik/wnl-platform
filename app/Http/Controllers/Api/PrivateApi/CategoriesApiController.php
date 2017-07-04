<?php

namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoriesApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.categories');
	}

	public function get($id) {
		$rootCategories = Category::where('parent_id', null)->get(['id', 'name']);

		foreach($rootCategories as $rootCategory) {
			$rootCategory['categories'] = Category::where('parent_id', $rootCategory->id)->get(['id', 'name']);
		}
		return $this->json($rootCategories);
	}
}
