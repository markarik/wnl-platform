<?php namespace App\Http\Controllers\Api\PrivateApi;

use Auth;
use Storage;
use Facades\Lib\Bethink\Bethink;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Api\ApiController;

class UploadApiController extends ApiController
{
	const AVATAR_ALLOWED_TYPES = ['image/gif', 'image/jpeg', 'image/png'];
	const AVATAR_MAX_FILE_SIZE = '10000000';

	public function post(Request $request)
	{
		$user = Auth::user();

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
			$path = 'uploads/' . str_random(32) . '.gif';
			$contents = file_get_contents($file->getPathname());
			Storage::put('public/' . $path, $contents, 'public');

			return $this->respondOk(Bethink::getAssetPublicUrl($path));
		}

		$image = Image::make($file)->resize(2000, 2000, function ($constraint) {
			$constraint->aspectRatio();
		})->stream('jpg', 80);
		$path = 'uploads/' . str_random(32) . '.jpg';
		Storage::put('public/' . $path, $image->__toString(), 'public');

		return $this->respondOk(Bethink::getAssetPublicUrl($path));
	}
}
