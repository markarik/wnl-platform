<?php
namespace App\Http\Controllers\Api\Concerns;

use Auth;
use Illuminate\Http\Request;
use ScoutEngines\Elasticsearch\Searchable;

trait PerformsApiSearches
{
	public function search(Request $request)
	{
		$resource = $request->route('resource');
		$model = static::getResourceModel($resource);
		$phrase = $request->q;
		$user = Auth::user();

		if (empty($phrase)) {
			return $this->respondOk([]);
		}

		if (!array_key_exists(Searchable::class, class_uses($model))) {
			return $this->respondNotImplemented();
		}

		$model::savePhrase(['phrase' => $phrase, 'user_id' => $user->id]);

		$query = $this->buildQuery($phrase);
		$raw = $model::searchRaw($query);

		return $this->respondOk($raw);
	}


	protected function buildQuery($query)
	{
		// Right now it's tightly coupled with slides
		// next step - decouple
		$params = [
			'body'    => [
				'from'      => 0,
				'size'      => 32,
				'query'     => [
					'bool' => [
						'should' => [
							["constant_score" => [
								'query' => ['match_phrase' => [
									'snippet.header' => [
										"query" => $query,
									],
								],
								],
								'boost' => 100,
							],
							],
							["constant_score" => [
								'query' => ['match_phrase' => [
									'snippet.content' => [
										"query" => $query,
									],
								]],
								'boost' => 90,
							],
							],
							["constant_score" => [
								'query' => ['match_phrase_prefix' => [
									'snippet.header' => [
										"query" => $query,
									],
								]],
								'boost' => 10,
							],
							],
							["constant_score" => [
								'query' => ['match_phrase_prefix' => [
									'snippet.content' => [
										"query" => $query,
									],
								]],
								'boost' => 9,
							],
							],
							['multi_match' => [
								"query"  => $query,
								'fields' => ['snippet.content', 'snippet.header^5'],
								"type"   => "cross_fields",
							]],
							['query_string' => [
								"query"            => $query,
								'fields'           => ['snippet.content'],
								"default_operator" => "and",
							]],
							['query_string' => [
								'query'            => "*{$query}*",
								'analyze_wildcard' => true,
								'fields'           => ['snippet.header', 'snippet.content'],
							]],
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

		if (!preg_match('/\s/', $query)) {
			$params['body']['query']['bool']['should'][] = [
				'query_string' => [
					'query'            => "{$query}~",
					'analyze_wildcard' => true,
					'fields'           => ['snippet.header^5', 'snippet.content'],
				]
			];
		}

		return $params;
	}
}
