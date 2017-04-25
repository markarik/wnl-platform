<?php


namespace App\Http\Controllers\Api\PrivateApi\User;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Http\Request;

class UserAvatarApiController extends ApiController
{
	const AVATAR_ALLOWED_TYPES = ['image/gif', 'image/jpeg', 'image/png'];
	const AVATAR_MAX_FILE_SIZE = '10000000';

	public function post(Request $request)
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
