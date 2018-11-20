<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\UpdateFlashcard;
use App\Models\Flashcard;
use Illuminate\Http\Request;

class FlashcardsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.flashcards');
	}

	public function put(UpdateFlashcard $request)
	{
		$flashcard = Flashcard::find($request->route('id'));

		if (!$flashcard) {
			return $this->respondNotFound();
		}

		$flashcard->update($request->all());
		$flashcard->tags()->sync($request->get('tags'));

		return $this->transformAndRespond($flashcard);
	}

	public function post(UpdateFlashcard $request)
	{
		$flashcard = new Flashcard($request->all());
		$flashcard->save();

		$flashcard->tags()->sync($request->get('tags'));

		return $this->transformAndRespond($flashcard);
	}
}
