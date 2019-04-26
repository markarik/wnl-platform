<?php namespace App\Http\Controllers\Api\PrivateApi;

use Storage;
use App\Models\User;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\ProfileTransformer;

class UserAvatarApiController extends ApiController
{
	const AVATAR_ALLOWED_TYPES = ['image/gif', 'image/jpeg', 'image/png'];
	const AVATAR_MAX_FILE_SIZE = '10000000';
	const AVATAR_MAX_SIZE_PX = 200;

	public function post(Request $request)
	{
		$user = User::fetch($request->route('id'));

		if (!$request->hasFile('file')) {
			return $this->respondInvalidInput('The request contained no file.');
		}

		$file = $request->file;

		if (!$file->isValid()) {
			return $this->respondInvalidInput('File upload failed.');
		}

		if (!in_array($file->getClientMimeType(), self::AVATAR_ALLOWED_TYPES)) {
			return $this->respondInvalidInput('Unsupported file type.');
		}

		if ($file->getClientSize() > self::AVATAR_MAX_FILE_SIZE) {
			return $this->respondInvalidInput('Max. allowed file size exceeded.');
		}

		if ($file->getClientMimeType() === 'image/gif') {
			$path = $file->store('avatars', 'public');
		} else {
			$path = $this->storeStaticImage($file);
		}

		$user->profile->avatar = $path;
		$user->profile->save();

		$resource = new Item($user->profile, new ProfileTransformer, 'user_profile');
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function storeStaticImage($file)
	{
		$image = Image::make($file)->fit(self::AVATAR_MAX_SIZE_PX)->stream('png');
		$path = 'avatars/' . str_random(32) . '.png';
		Storage::put('public/' . $path, $image->__toString(), 'public');
		return $path;
	}
}
