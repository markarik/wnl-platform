<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
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
		// TODO update parent

		if (!$taxonomyTerm) {
			return $this->respondNotFound();
		}

		$taxonomyTerm->update($request->all());

		return $this->transformAndRespond($taxonomyTerm);
	}

	public function move(Request $request) {
		$target = TaxonomyTerm::find($request->get('term'));
		$direction = $request->get('direction');

		if ($direction > 0) {
			$target->down();
		} else {
			$target->up();
		}

		return $this->respondOk();
	}
}
