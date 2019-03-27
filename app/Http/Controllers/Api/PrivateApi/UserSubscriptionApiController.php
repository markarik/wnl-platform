<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Auth;

class UserSubscriptionApiController extends ApiController {

	public function getSubscription() {
		$user = Auth::User();

		return $this->respondOk([
			'status' => $user->subscriptionStatus,
			'dates' => $user->subscriptionDates,
			'latest_product' => $user->hasLatestCourseProduct,
		]);
	}
}
