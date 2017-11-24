<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Page;
use Illuminate\Http\Request;

class PagesApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.pages');
	}

	public function get($slug)
	{
		$page = Page::where('slug', $slug)->first();
		if (!$page) return $this->respondNotFound();

		return $this->json($this->transform($page));
	}
}
