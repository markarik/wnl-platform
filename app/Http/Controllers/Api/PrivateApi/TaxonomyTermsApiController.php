<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\TaxonomyTerm;
use Illuminate\Http\Request;

class TaxonomyTermsApiController extends ApiController {
	public function __construct(Request $request) {
		parent::__construct($request);
		$this->resourceName = config('papi.resources.taxonomy-terms');
	}

	public function getByTaxonomy($taxonomyId) {
		return $this->transformAndRespond(TaxonomyTerm::where('taxonomy_id', $taxonomyId)->get()->toFlatTree());
	}
}