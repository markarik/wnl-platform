<?php namespace App\Http\Controllers\Api\PrivateApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Tag;

class QuizQuestionsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz-questions');
	}

	public function getFilters() {
		$chronoTags = Tag::where('name', 'like', 'lek-%')->get();
		$subjectsTags = Tag::whereIn('name', [
			'Kardiologia',
			'Pulmonologia',
			'Gastroenterologia',
			'Endokrynologia',
			'Hematologia',
			'Nefrologia',
			'Reumatologia',
			'Diabetologia',
			'Laryngologia'
		])->get();

		return $this->respondOk([
			'chrono' => $chronoTags->toArray(),
			'subjects' => $subjectsTags->toArray()
		]);
	}
}
