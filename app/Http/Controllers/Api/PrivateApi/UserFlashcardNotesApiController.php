<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Flashcard;
use App\Models\User;
use App\Models\UserFlashcardNote;
use App\Models\UserFlashcardsResults;
use Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;


class UserFlashcardNotesApiController extends ApiController
{
	use DispatchesJobs;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.user-flashcard-notes');
	}

	public function post(Request $request) {
		$flashcardId = $request->route('flashcardId');

		if (!Flashcard::find($flashcardId)) {
			return $this->respondNotFound();
		}

		$result = UserFlashcardNote::create([
			'user_id' => Auth::user()->id,
			'flashcard_id' => $flashcardId,
			'note' => $request->get('note')
		]);

		return $this->respondOk($result);
	}
}
