<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\AttachTaxonomyTerm;
use App\Http\Requests\Course\MoveTaxonomyTerm;
use App\Http\Requests\Course\UpdateTaxonomyTerm;
use App\Models\Annotation;
use App\Models\Flashcard;
use App\Models\QuizQuestion;
use App\Models\Slide;
use App\Models\TaxonomyTerm;
use App\Models\TaxonomyTermable;
use Illuminate\Http\Request;

class TaxonomyTermsApiController extends ApiController {
	const TAXONOMY_TERMABLE_TYPES = [
		[
			'requestParam' => 'annotations',
			'className' => Annotation::class,
		],
		[
			'requestParam' => 'flashcards',
			'className' => Flashcard::class,
		],
		[
			'requestParam' => 'quiz_questions',
			'className' => QuizQuestion::class,
		],
		[
			'requestParam' => 'slides',
			'className' => Slide::class,
		],
	];

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

		$insert = [];

		foreach (self::TAXONOMY_TERMABLE_TYPES as $taxonomyTermableType) {
			$requestParamValue = $request[$taxonomyTermableType['requestParam']];

			if (!empty($requestParamValue)) {
				foreach ($requestParamValue as $termableId) {
					$insert []= [
						'taxonomy_term_id' => $taxonomyTerm->id,
						'taxonomy_termable_id' => $termableId,
						'taxonomy_termable_type' => $taxonomyTermableType['className']
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

	public function detach(AttachTaxonomyTerm $request, $id) {
		$taxonomyTerm = TaxonomyTerm::find($id);

		if (!$taxonomyTerm) {
			return $this->respondNotFound('Taxonomy term does not exist');
		}

		$pdo = \DB::connection()->getPdo();
		$tuplesToDelete = [];

		foreach (self::TAXONOMY_TERMABLE_TYPES as $taxonomyTermableType) {
			$requestParamValue = $request[$taxonomyTermableType['requestParam']];

			if (!empty($requestParamValue)) {
				foreach ($requestParamValue as $termableId) {
					$tuplesToDelete []= "(" .
						$pdo->quote($taxonomyTerm->id) .
						", " .
						$pdo->quote($termableId) .
						", " .
						$pdo->quote($taxonomyTermableType['className']) .
						")";
				}
			}
		}

		\DB::statement(
			'delete from taxonomy_termables where (taxonomy_term_id, taxonomy_termable_id, taxonomy_termable_type) in (' .
				implode(',', $tuplesToDelete) .
				')'
		);

		return $this->respondOk();
	}
}
