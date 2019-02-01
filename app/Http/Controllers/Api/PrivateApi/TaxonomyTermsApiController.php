<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Concerns\OperatesOnNestedSets;
use App\Http\Requests\Course\UpdateTaxonomyTermRelations;
use App\Http\Requests\Course\MoveTaxonomyTerm;
use App\Http\Requests\Course\UpdateTaxonomyTerm;
use App\Models\TaxonomyTerm;
use Illuminate\Http\Request;

class TaxonomyTermsApiController extends ApiController
{
	use OperatesOnNestedSets;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.taxonomy-terms');
	}

	public function getByTaxonomy($taxonomyId)
	{
		return $this->transformAndRespond(TaxonomyTerm::where('taxonomy_id', $taxonomyId)
			->defaultOrder()
			->get()
			->toFlatTree()
		);
	}

	public function post(UpdateTaxonomyTerm $request)
	{
		return $this->postNode($request);
	}

	public function put(UpdateTaxonomyTerm $request)
	{
		return $this->putNode($request);
	}

	public function move(MoveTaxonomyTerm $request)
	{
		return $this->moveNode($request);
	}

	public function attach(UpdateTaxonomyTermRelations $request, $id) {
		$taxonomyTerm = TaxonomyTerm::find($id);

		if (!empty($request['annotations'])) {
			$taxonomyTerm->annotations()->syncWithoutDetaching($request['annotations']);
		}

		if (!empty($request['flashcards'])) {
			$taxonomyTerm->flashcards()->syncWithoutDetaching($request['flashcards']);
		}

		if (!empty($request['quiz_questions'])) {
			$taxonomyTerm->quizQuestions()->syncWithoutDetaching($request['quiz_questions']);
		}

		if (!empty($request['slides'])) {
			$taxonomyTerm->slides()->syncWithoutDetaching($request['slides']);
		}

		return $this->respondOk();
	}

	public function detach(UpdateTaxonomyTermRelations $request, $id) {
		$taxonomyTerm = TaxonomyTerm::find($id);

		if (!empty($request['annotations'])) {
			$taxonomyTerm->annotations()->detach($request['annotations']);
		}

		if (!empty($request['flashcards'])) {
			$taxonomyTerm->flashcards()->detach($request['flashcards']);
		}

		if (!empty($request['quiz_questions'])) {
			$taxonomyTerm->quizQuestions()->detach($request['quiz_questions']);
		}

		if (!empty($request['slides'])) {
			$taxonomyTerm->slides()->detach($request['slides']);
		}

		return $this->respondOk();
	}
}
