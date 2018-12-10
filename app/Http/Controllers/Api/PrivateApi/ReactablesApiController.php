<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Models\Reaction;
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
		$request->validate([
			'reactable_id' => 'array'
		]);

		$user = User::fetch($userId);

		if (Auth::user()->id !== $user->id) {
			return $this->respondForbidden();
		}

		$reactables = Reactable::where('reactable_type', 'App\\Models\\Slide')
			->select('reactables.*')
			->where('reactables.user_id', $user->id)
			->whereIn('reactables.reactable_id', $request->get('slideIds'))
			->join('reactions', 'reactions.id', '=', 'reactables.reaction_id')
			->whereIn('reactions.type', ['watch', 'bookmark'])
			->get();

		return $this->transformAndRespond($reactables);
	}
}
