<?php namespace App\Http\Controllers\Api\PrivateApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Tag;
use App\Models\Taxonomy;

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
		// TODO id shouldn't be hardcoded here
		$taxonomyTags = Taxonomy::find(1)->tagsTaxonomy->sortBy("order_number");
		$examsFilterItems = [];

		foreach($taxonomyTags as $taxonomyTag) {
			$examsFilterItems[] = [
				'name' => $taxonomyTag->tag->name,
				'value' => $taxonomyTag->tag->id
			];
		}

		return $this->respondOk([
			'exams' => [
				'type' => 'tags',
				'items' => $examsFilterItems
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
