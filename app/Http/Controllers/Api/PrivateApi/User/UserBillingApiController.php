<?php namespace App\Http\Controllers\Api\PrivateApi\User;

use Auth;
use App\Models\User;
use League\Fractal\Resource\Item;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\UpdateUserBilling;
use App\Http\Controllers\Api\Transformers\UserBillingTransformer;

class UserBillingApiController extends ApiController
{
	public function get($id)
	{
		$user = User::fetch($id);
		$billingData = $user->billing()->first();

		if (!$user || !$billingData) {
			return $this->respondNotFound();
		}

		if (!Auth::user()->can('view', $billingData)) {
			return $this->respondUnauthorized();
		}

		$resource = new Item($billingData, new UserBillingTransformer, 'user_billing');
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function put(UpdateUserBilling $request)
	{
		$user = User::fetch($request->route('id'));
		$user->billing()->updateOrCreate(['user_id' => $user->id], $request->all());

		return $this->respondOk();
	}
}