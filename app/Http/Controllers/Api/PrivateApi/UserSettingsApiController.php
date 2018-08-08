<?php namespace App\Http\Controllers\Api\PrivateApi;

use Auth;
use App\Models\User;
use League\Fractal\Resource\Item;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\UpdateUserSettings;
use App\Http\Controllers\Api\Transformers\UserSettingsTransformer;

class UserSettingsApiController extends ApiController
{
	public function get($id)
	{
		$user = User::fetch($id);
		$settings = $user->settings()->first();

		if (!$user) {
			return $this->respondNotFound();
		}

		if (!$settings) {
			$settings = config('user-default-settings');

			return $this->respondOk($settings);
		}

		if (!Auth::user()->can('view', $settings)) {
			return $this->respondForbidden();
		}

		$resource = new Item($settings, new UserSettingsTransformer, 'user_settings');
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function put(UpdateUserSettings $request)
	{
		$user = User::fetch($request->route('id'));
		$user->settings()->updateOrCreate(
			['user_id' => $user->id],
			['settings' => $request->all()]
		);

		return $this->respondOk();
	}
}
