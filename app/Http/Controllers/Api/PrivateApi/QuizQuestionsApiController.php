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
		// TODO id shouldn't be hardcoded here
		$examsFilterItems = $this->buildTaxonomyStructure(1);
		$subjectsFilterItems = $this->buildTaxonomyStructure(2);

		return $this->respondOk([
			'exams' => [
				'type' => 'tags',
				'items' => $examsFilterItems
			],
			'subjects' => [
				'type' => 'tags',
				'items' => $subjectsFilterItems
			]
		]);
	}

	protected function getChildItems($expectedParent, $list) {
		return $list->first(function ($value, $key) use ($expectedParent) {
			return $key === $expectedParent;
		});
	}

	protected function buildChildStructure($tagId, $groupedTags, $structure) {
		if (!$groupedTags->has($tagId)) {
			return null;
		}

		$root = $this->getChildItems($tagId, $groupedTags);

		foreach($root as $rootItem) {
			$entry = [];
			$entry = [
				'name' => $rootItem->tag->name,
				'value' => $rootItem->tag->id
			];

			$childStructure = $this->buildChildStructure($rootItem->tag->id, $groupedTags, []);

			if (!empty($childStructure)) {
				$entry['items'] = $childStructure;
			}

			$structure[] = $entry;
		}


		return $structure;
	}

	protected function buildTaxonomyStructure($taxonomyId) {
		$groupedTags = Taxonomy::find($taxonomyId)->tagsTaxonomy->sortBy('order_number')->groupBy('parent_tag_id');
		$items = $this->buildChildStructure("", $groupedTags, []);


		return $items;
	}
}
