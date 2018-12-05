<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Reaction;
use App\Models\Slide;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class UserCollectionsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.user-collections');
	}

	public function delete($userId)
	{

		$user = User::find($userId);

		if (\Auth::user()->id !== $user->id) {
			return $this->respondForbidden();
		}

		$bookmark = Reaction::type('bookmark');

		$user->reactables()
			->whereIn('reactable_type', [
				'App\Models\QuizQuestion',
				'App\Models\Slide',
				'App\Models\QnaQuestion',
			])
			->where('reaction_id', $bookmark->id)
			->delete();

		return $this->respondOk();
	}
}
