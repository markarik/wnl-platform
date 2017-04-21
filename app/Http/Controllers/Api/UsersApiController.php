<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Transformers\UserProfileTransformer;
use App\Http\Controllers\Api\Transformers\UserTransformer;
use App\Http\Requests\User\UpdateUser;
use App\Http\Requests\User\UpdateUserProfile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Resource\Item;

class UsersApiController extends ApiController
{
	const AVATAR_ALLOWED_TYPES = ['image/gif', 'image/jpeg', 'image/png'];
	const AVATAR_MAX_FILE_SIZE = '10000000';

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.users');
	}

	public function get($id)
	{
		$user = User::fetch($id);
		$resource = new Item($user, new UserTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function put(UpdateUser $request)
	{
		$user = User::fetch($request->route('id'));
		$user->update($request->all());

		return $this->respondOk();
	}

	public function getUserProfile($id)
	{
		$user = User::fetch($id);
		$profile = $user->profile;

		if (!$user || !$profile) {
			return $this->respondNotFound();
		}

		$resource = new Item($profile, new UserProfileTransformer, 'user_profile');
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function putUserProfile(UpdateUserProfile $request)
	{
		$user = User::fetch($request->route('id'));
		$user->profile()->updateOrCreate(['user_id' => $user->id], $request->all());

		return $this->respondOk();
	}

	public function putUserAvatar(Request $request)
	{
		$user = User::fetch($request->route('id'));

		if (!$request->hasFile('file')) {
			return $this->respondInvalidInput([], 'The request contained no file.');
		}

		$file = $request->file;

		if (!$file->isValid()) {
			return $this->respondInvalidInput([], 'File upload failed.');
		}

		if (!in_array($file->getClientMimeType(), self::AVATAR_ALLOWED_TYPES)) {
			return $this->respondInvalidInput([], 'Unsupported file type.');
		}

		if ($file->getClientSize() > self::AVATAR_MAX_FILE_SIZE) {
			return $this->respondInvalidInput([], 'Max. allowed file size exceeded.');
		}

		$user->profile->avatar = $file->store('avatars', 'public');
		$user->profile->save();

		return $this->respondOk();
	}
}
