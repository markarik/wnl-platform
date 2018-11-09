<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\UpdateFlashcardsSet;
use App\Models\FlashcardsSet;
use Illuminate\Http\Request;

class FlashcardsSetsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.flashcards-sets');
	}

	public function put(UpdateFlashcardsSet $request)
	{
		$flashcardsSet = FlashcardsSet::find($request->route('id'));

		if (!$flashcardsSet) {
			return $this->respondNotFound();
		}

		$flashcardsSet->update($request->all());

		return $this->respondOk($flashcardsSet);
	}

	public function post(UpdateFlashcardsSet $request)
	{
		$flashcardsSet = new FlashcardsSet($request->all());

		$flashcardsSet->save();

		return $this->respondOk($flashcardsSet);
	}
}
