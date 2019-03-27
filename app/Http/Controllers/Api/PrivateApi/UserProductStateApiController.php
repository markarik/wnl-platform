<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Requests\User\UpdateUserProductState;
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

	public function updateForLatestProduct(UpdateUserProductState $request)
	{
		$user = User::fetch($request->route('id'));

		$productId = $user->getLatestPaidCourseProductId();

		$userProductState = $user->userProductStates()->updateOrCreate(
			[
				'user_id' => $user->id,
				'product_id' => $productId
			],
			[
				'onboarding_step' => $request->get('onboarding_step'),
			]
		);

		return $this->transformAndRespond($userProductState);
	}
}
