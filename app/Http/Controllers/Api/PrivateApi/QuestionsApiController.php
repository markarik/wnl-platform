<?php namespace App\Http\Controllers\Api\PrivateApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\QuizQuestion;

class QuestionsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.questions');
	}

	public function all() {
		return $this->respondOk(QuizQuestion::all()->pluck('text', 'id'));
	}
}
