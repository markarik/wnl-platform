<?php namespace App\Http\Controllers\Api\PrivateApi;

use Auth;
use App\Models\User;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\UpdateUserSettings;
use Illuminate\Http\Request;

class UserProductStateApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.user-product-state');
	}

	public function getForLatestProduct($userId)
	{
		$user = User::fetch($userId);

		if (!$user) {
			return $this->respondNotFound();
		}

		$userProductState = $user->userProductStates()->firstOrNew(['product_id' => $user->getLatestPaidCourseProductId()]);

		if (!Auth::user()->can('view', $userProductState)) {
			return $this->respondForbidden();
		}

		return $this->transformAndRespond($userProductState);
	}

	public function put(UpdateUserSettings $request)
	{
		// TODO
//		$user = User::fetch($request->route('id'));
//		$user->settings()->updateOrCreate(
//			['user_id' => $user->id],
//			['settings' => $request->all()]
//		);
//
//		return $this->respondOk();
	}
}
