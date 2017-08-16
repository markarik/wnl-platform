<?php namespace App\Http\Controllers\Api\PrivateApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\QuizQuestion;
use App\Models\Tag;

class QuestionsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.questions');
	}

	public function all() {
		return $this->respondOk(QuizQuestion::all()->toArray());
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
