<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Models\User;
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

	public function getSavedSlidesForUser(Request $request, $userId) {
		$user = User::fetch($userId);

		if (Auth::user()->id !== $user->id) {
			return $this->respondForbidden();
		}

		$reactables = Reactable::where('reactable_type', 'App\\Models\\Slide')
			->where('user_id', $user->id)
			->whereIn('reactable_id', $request->get('slideIds'))
			->whereIn('reaction_id', [4,5])
			->get();

		return $this->transformAndRespond($reactables);
	}
}
