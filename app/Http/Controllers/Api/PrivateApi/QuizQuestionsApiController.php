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
		/***
			[
				type: 'tags',
				items: [
					['name' => 'Interna', 'value' => 1, 'items': [
						['name' => 'Kardiologia', 'value' => 64]
					]],
					['name' => 'Pediatria', 'value' => 2],
				]
			]

		**/
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
			'exams' => [
				'type' => 'tags',
				'items' => [
					[
						'name' => Tag::find(145)->name,
						'value' => Tag::find(145)->id
					],
					[
						'name' => Tag::find(120)->name,
						'value' => Tag::find(120)->id
					],
					[
						'name' => Tag::find(154)->name,
						'value' => Tag::find(154)->id
					],
					[
						'name' => Tag::find(110)->name,
						'value' => Tag::find(110)->id
					],
					[
						'name' => Tag::find(144)->name,
						'value' => Tag::find(144)->id
					],
				]
			],
			'subjects' => [
				'type' => 'tags',
				'items' => [
					[
						'name' => Tag::where('name', 'Interna')->first()->name,
						'value' => Tag::where('name', 'Interna')->first()->id,
						'type' => 'tags',
						'items' => [
							[
								'name' => Tag::where('name', 'Kardiologia')->first()->name,
								'value' => Tag::where('name', 'Kardiologia')->first()->id
							],
							[
								'name' => Tag::where('name', 'Pulmonologia')->first()->name,
								'value' => Tag::where('name', 'Pulmonologia')->first()->id
							],
							[
								'name' => Tag::where('name', 'Laryngologia')->first()->name,
								'value' => Tag::where('name', 'Laryngologia')->first()->id
							]
						]
					],
					[
						'name' => Tag::where('name', 'Pediatria')->first()->name,
						'value' => Tag::where('name', 'Pediatria')->first()->id,
						'type' => 'tags',
						'items' => [
							[
								'name' => Tag::where('name', 'Kardiologia pediatryczna')->first()->name,
								'value' => Tag::where('name', 'Kardiologia pediatryczna')->first()->id
							],
							[
								'name' => Tag::where('name', 'Pulmonologia pediatryczna')->first()->name,
								'value' => Tag::where('name', 'Pulmonologia pediatryczna')->first()->id
							],
							[
								'name' => Tag::where('name', 'Hematologia pediatryczna')->first()->name,
								'value' => Tag::where('name', 'Hematologia pediatryczna')->first()->id
							]
						]
					]
				]
			]
		]);
	}
}
