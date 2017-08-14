<?php namespace App\Http\Controllers\Api\PrivateApi;

use Auth;
use App\Models\Reactable;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;

class ReactablesApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.reactables');
	}

	public function filter(Request $request)
	{
		$user = Auth::user();

		$reactablesQuery = Reactable::where('user_id', $user->id);
		$reactablesQuery = $this->applyFilters($reactablesQuery, $request);
		$reactables = $reactablesQuery->get();

		return $this->transformAndRespond($reactables);
	}
}
