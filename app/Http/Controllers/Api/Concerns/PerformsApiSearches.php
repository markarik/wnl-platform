<?php
namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\Request;
use ScoutEngines\Elasticsearch\Searchable;

trait PerformsApiSearches
{
	public function search(Request $request)
	{
		$modelName = self::getResourceModel($this->resourceName);
		if (!$this->canSearch($modelName)) {
			return $this->respondNotImplemented();
		}

		if (empty($request->q)) {
			return $this->respondInvalidInput('query not set');
		}

		$escapedQuery = $this->escapeQuery($request->q);

		if (empty(trim($escapedQuery))) {
			return $this->respondInvalidInput('query value not supported');
		}

		$models = $modelName::search($escapedQuery)->get();
		$data = $this->transform($models);

		return $this->respondOk($data);
	}

	/**
	 * :facepalm:
	 *
	 * @param  String $query
	 *
	 * @return String
	 */
	protected function escapeQuery($query)
	{
		return preg_replace('/(\+|\-|\=|\&|\||\!|\(|\)|\{|\}|\[|\]|\^|\"|\~|\*|\<|\>|\?|\:|\\\\|\/)/', ' ', $query);
	}

	protected function canSearch($modelName) {
		return array_key_exists(Searchable::class, class_uses($modelName));
	}
}
