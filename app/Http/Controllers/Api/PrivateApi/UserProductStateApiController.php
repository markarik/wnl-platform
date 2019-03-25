<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Requests\User\UpdateUserProductState;
use Auth;
use App\Models\User;
use App\Http\Controllers\Api\ApiController;
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

	public function updateForLatestProduct(UpdateUserProductState $request)
	{
		$user = User::fetch($request->route('id'));

		if (!$user) {
			return $this->respondNotFound();
		}

		$productId = $user->getLatestPaidCourseProductId();

		$user->userProductStates()->updateOrCreate(
			[
				'user_id' => $user->id,
				'product_id' => $productId
			],
			[
				'onboarding_step' => $request->get('onboarding_step'),
			]
		);

		return $this->respondOk();
	}
}
