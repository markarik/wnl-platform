<?php


namespace App\Http\Controllers\Api\PrivateApi;

use Auth;
use Storage;
use App\Models\User;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\UserProfileTransformer;

class UploadApiController extends ApiController
{
	const AVATAR_ALLOWED_TYPES = ['image/gif', 'image/jpeg', 'image/png'];
	const AVATAR_MAX_FILE_SIZE = '10000000';

	public function post(Request $request)
	{
		$user = Auth::user();

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

		if ($file->getClientMimeType() === 'image/gif') {
			$path = $file->store('uploads', 'public');

			return $this->respondOk(asset('storage/' . $path));
		}

		$image = Image::make($file)->resize(2000, 2000, function ($constraint) {
			$constraint->aspectRatio();
		})->stream('jpg', 80);
		$path = 'uploads/' . str_random(32) . '.jpg';
		Storage::put('public/' . $path, $image);

		return $this->respondOk(asset('storage/' . $path));
	}
}
