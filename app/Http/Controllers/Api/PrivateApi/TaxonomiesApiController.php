<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\UpdateTaxonomy;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Http\Request;

class TaxonomiesApiController extends ApiController {
	public function __construct(Request $request) {
		parent::__construct($request);
		$this->resourceName = config('papi.resources.taxonomies');
	}

	public function post(UpdateTaxonomy $request) {
		$taxonomy = Taxonomy::create($request->all());

		return $this->transformAndRespond($taxonomy);
	}

	public function put(UpdateTaxonomy $request) {
		$taxonomy = Taxonomy::find($request->route('id'));

		if (!$taxonomy) {
			return $this->respondNotFound();
		}

		$taxonomy->update($request->all());

		return $this->transformAndRespond($taxonomy);
	}
}
