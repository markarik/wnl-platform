<?php namespace App\Http\Controllers\Api\PrivateApi;

use Auth;
use App\Models\User;
use App\Models\UserQuizResults;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\UserQuizResultsTransformer;

class UserQuizResultsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz_results');
	}

	public function get($userId)
	{
		$user = User::fetch($userId);

		if (!Auth::user()->can('view', $user)) {
			return $this->respondUnauthorized();
		}

		$resource = new Collection(UserQuizResults::where('user_id', $userId)->get(), new UserQuizResultsTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}
}
