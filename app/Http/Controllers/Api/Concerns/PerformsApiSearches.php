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


	protected function buildQuery($query)
	{
		// Right now it's tightly coupled with slides
		// next step - decouple
		$params = [
			'body' => [
				'query'     => [
					'bool' => [
						'should' => [
							[
								'query_string' => [
									'query'  => $query,
									'fields' => ['snippet.content'],
									'boost'  => 0.5,
								],
							],
							[
								'query_string' => [
									'query'  => $query,
									'fields' => ['snippet.header'],
									'boost'  => 1,
								],
							],
							[
								'match_phrase' => [
									'snippet.content' => [
										"query" => $query,
										'boost' => 1.5,
									],
								],
							],
							[
								'match_phrase' => [
									'snippet.header' => [
										"query" => $query,
										'boost' => 2,
									],
								],
							],
							[
								'query_string' => [
									'query'            => "*{$query}*",
									'analyze_wildcard' => true,
									'fields'           => ['snippet.header', 'snippet.content'],
									'boost'            => 0.5,
								],
							],
							[
								'query_string' => [
									'query'            => "{$query}~",
									'analyze_wildcard' => true,
									'fields'           => ['snippet.header', 'snippet.content'],
									'boost'            => 0.1,
								],
							],
						],
					],
				],
				'highlight' => [
					'fields' => [
						'snippet.content' => [
							'fragment_size' => 5000,
						],
						'snippet.header'  => [
							'fragment_size' => 5000,
						],
					],
				],
			],
		];

		return $params;
	}
}
