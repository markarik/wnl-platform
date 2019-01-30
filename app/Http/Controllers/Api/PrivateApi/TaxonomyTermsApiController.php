<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\AttachTaxonomyTerm;
use App\Http\Requests\Course\MoveTaxonomyTerm;
use App\Http\Requests\Course\UpdateTaxonomyTerm;
use App\Models\TaxonomyTerm;
use Illuminate\Http\Request;

class TaxonomyTermsApiController extends ApiController {

	public function __construct(Request $request) {
		parent::__construct($request);
		$this->resourceName = config('papi.resources.taxonomy-terms');
	}

	public function getByTaxonomy($taxonomyId) {
		return $this->transformAndRespond(TaxonomyTerm::where('taxonomy_id', $taxonomyId)
			->defaultOrder()
			->get()
			->toFlatTree()
		);
	}

	public function post(UpdateTaxonomyTerm $request) {
		$parentTaxonomyTerm = null;
		if ($request->parent_id) {
			$parentTaxonomyTerm = TaxonomyTerm::find($request->parent_id);
		}

		$taxonomyTerm = TaxonomyTerm::create($request->all(), $parentTaxonomyTerm);

		return $this->transformAndRespond($taxonomyTerm);
	}

	public function put(UpdateTaxonomyTerm $request) {
		$taxonomyTerm = TaxonomyTerm::find($request->route('id'));
		$newParentId = $request->get('parent_id');

		if ($newParentId !== $taxonomyTerm->parent_id) {
			if ($newParentId === null) {
				$taxonomyTerm->makeRoot();
			} else {
				$taxonomyTerm->appendToNode(TaxonomyTerm::find($newParentId));
			}
		}

		if (!$taxonomyTerm) {
			return $this->respondNotFound();
		}

		$taxonomyTerm->update($request->all());

		return $this->transformAndRespond($taxonomyTerm);
	}

	public function move(MoveTaxonomyTerm $request) {
		$target = TaxonomyTerm::find($request->get('term_id'));
		$direction = $request->get('direction');

		if ($direction === 0) {
			return $this->respondOk();
		}

		if ($direction > 0) {
			$success = $target->down($direction);
		} else {
			$success = $target->up(abs($direction));
		}

		if (!$success) {
			return $this->respondUnprocessableEntity('direction out of range');
		}

		return $this->respondOk();
	}

	public function attach(AttachTaxonomyTerm $request, $id) {
		$taxonomyTerm = TaxonomyTerm::find($id);

		if (!$taxonomyTerm) {
			return $this->respondNotFound('Taxonomy term does not exist');
		}

		if (!empty($request['annotations'])) {
			$taxonomyTerm->annotations()->attach($request['annotations']);
		}

		if (!empty($request['flashcards'])) {
			$taxonomyTerm->flashcards()->attach($request['flashcards']);
		}

		if (!empty($request['quiz_questions'])) {
			$taxonomyTerm->quizQuestions()->attach($request['quiz_questions']);
		}

		if (!empty($request['slides'])) {
			$taxonomyTerm->slides()->attach($request['slides']);
		}

		return $this->respondOk();
	}

	public function detach(AttachTaxonomyTerm $request, $id) {
		$taxonomyTerm = TaxonomyTerm::find($id);

		if (!$taxonomyTerm) {
			return $this->respondNotFound('Taxonomy term does not exist');
		}

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
