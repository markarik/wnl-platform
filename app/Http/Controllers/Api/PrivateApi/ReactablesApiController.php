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

		$savedReactions = Reaction::whereIn('name', ['watch', 'bookmark'])->get()->pluck('id');

		$reactables = Reactable::where('reactable_type', 'App\\Models\\Slide')
			->where('user_id', $user->id)
			->whereIn('reactable_id', $request->get('slideIds'))
			->whereIn('reaction_id', $savedReactions)
			->get();

		return $this->transformAndRespond($reactables);
	}
}
