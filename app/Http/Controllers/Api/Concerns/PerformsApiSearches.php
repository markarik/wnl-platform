<?php
namespace App\Http\Controllers\Api\Concerns;


use Illuminate\Http\Request;
use ScoutEngines\Elasticsearch\Searchable;

trait PerformsApiSearches
{
	public function search(Request $request)
	{
		$resource = $request->route('resource');
		$model = static::getResourceModel($resource);
		$queryParameter = $request->q;

		if (empty($queryParameter)) {
			return $this->respondOk([]);
		}

		if (!array_key_exists(Searchable::class, class_uses($model))) {
			return $this->respondNotImplemented();
		}

		$query = $this->buildQuery($queryParameter);
		$raw = $model::searchRaw($query);

		return $this->respondOk($raw);
	}


	protected function buildQuery($query) {
		$params = [
			'body'  => [
				'query'     => [
					'bool' => [
						'must' => [
							[
								'query_string' => [
									'query'            => "*{$query}* OR {$query}~",
									'analyze_wildcard' => true,
									'all_fields'       => true,
								],
							],
						],
					],
				],
				// Right now it's tightly coupled with slides
				// next step - decouple
				'highlight' => [
					'fields' => [
						'snippet.content' => new \stdClass(),
						'snippet.header'  => new \stdClass(),
					],
				],
			],
		];

		return $params;
	}
}
