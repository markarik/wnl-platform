<?php namespace App\Http\Controllers\Api\PrivateApi;


use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\ScreenTransformer;
use App\Http\Requests\Course\UpdateScreen;
use App\Models\Screen;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;

class ScreensApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.screens');
	}

	public function put(UpdateScreen $request)
	{
		$screen = Screen::find($request->route('id'));

		if (!$screen) {
			return $this->respondNotFound();
		}
		$screen->update([
			'content' => $request->input('content'),
			'meta' => json_decode($request->input('meta')),
			'type' => $request->input('type'),
			'name' => $request->input('name'),
		]);

		CoursesApiController::clearCache();

		return $this->respondOk();
	}

	public function patch(UpdateScreen $request)
	{
		$screen = Screen::find($request->route('id'));

		if (!$screen) {
			return $this->respondNotFound();
		}

		$screen->update($request->all());

		CoursesApiController::clearCache();

		return $this->respondOk();
	}

	public function post(UpdateScreen $request)
	{
		$serializedData = $request->all();
		$serializedData['meta'] = json_decode($request->get('meta'));

		$screen = Screen::create($serializedData);
		$resource = new Item($screen, new ScreenTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		CoursesApiController::clearCache();

		return $this->respondOk($data);
	}
}
