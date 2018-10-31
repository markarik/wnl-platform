<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Flashcard;
use App\Models\User;
use App\Models\UserFlashcardsResults;
use Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;


class UserFlashcardsResultsApiController extends ApiController
{
	use DispatchesJobs;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.user-flashcards-results');
	}

	public function post(Request $request) {
		$userId = $request->route('userId');
		$flashcardId = $request->route('flashcardId');
		$user = User::fetch($userId);
		$answer = $request->get('answer');

		if (!Auth::user()->can('view', $user)) {
			return $this->respondForbidden();
		}

		if (!Flashcard::find($flashcardId)) {
			return $this->respondNotFound();
		}

		$result = UserFlashcardsResults::create([
			'user_id' => $userId,
			'flashcard_id' => $flashcardId,
			'answer' => $answer
		]);

		return $this->respondOk($result);
	}
}
