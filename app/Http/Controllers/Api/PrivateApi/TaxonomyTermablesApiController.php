<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\UpdateTaxonomyTermables;
use App\Models\TaxonomyTerm;
use App\Models\TaxonomyTermable;
use Illuminate\Http\Request;

class TaxonomyTermablesApiController extends ApiController {
	public function __construct(Request $request) {
		parent::__construct($request);
		$this->resourceName = config('papi.resources.taxonomy-termables');
	}

	public function put(UpdateTaxonomyTermables $request, $id) {
		$taxonomyTerm = TaxonomyTerm::find($id);

		if (!$taxonomyTerm) {
			return $this->respondNotFound('Taxonomy term does not exist');
		}

		$insert = [];
		$insertables = [
			[
				'field' => 'annotations',
				'type' => 'App\\Models\\Annotation',
			],
			[
				'field' => 'flashcards',
				'type' => 'App\\Models\\Flashcard',
			],
			[
				'field' => 'quiz_questions',
				'type' => 'App\\Models\\QuizQuestion',
			],
			[
				'field' => 'slides',
				'type' => 'App\\Models\\Slide',
			],
		];

		foreach ($insertables as $insertable) {
			$fieldValue = $request[$insertable['field']];

			if (!empty($fieldValue)) {
				foreach ($fieldValue as $termableId) {
					$insert []= [
						'taxonomy_term_id' => $taxonomyTerm->id,
						'taxonomy_termable_id' => $termableId,
						'taxonomy_termable_type' => $insertable['type']
					];
				}
			}
		}

		TaxonomyTermable::insertOnDuplicateKey($insert, [
			// Ignore duplicate keys without ignoring other errors
			// See https://stackoverflow.com/questions/548541/insert-ignore-vs-insert-on-duplicate-key-update
			'id' => 'id'
		]);

		return $this->respondOk();
	}
}
