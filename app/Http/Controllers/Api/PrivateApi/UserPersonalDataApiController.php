<?php namespace App\Http\Controllers\Api\PrivateApi;

use Auth;
use App\Models\User;
use App\Models\UserPresonalData;
use League\Fractal\Resource\Item;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\PostUserPersonalData;
use App\Http\Controllers\Api\Transformers\UserPersonalDataTransformer;

class UserPersonalDataApiController extends ApiController
{
    public function get($id)
    {
        $user = User::fetch($id);
        $personalData = $user->personalData()->first();

        if (!$user || !$personalData) {
			return $this->respondNotFound();
		}

        if (!Auth::user()->can('view', $personalData)) {
			return $this->respondForbidden();
		}

        $resource = new Item($personalData, new UserPersonalDataTransformer, 'user_personal_data');
        $data = $this->fractal->createData($resource)->toArray();

        return $this->respondOk($data);
    }

    public function post(PostUserPersonalData $request)
    {
        $user = User::fetch($request->userId);

        if ($request->personal_identity_number) {
            dd();
        }
    }
}
