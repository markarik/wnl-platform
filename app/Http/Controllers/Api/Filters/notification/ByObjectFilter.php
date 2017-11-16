<?php namespace App\Http\Controllers\Api\Filters\Notification;


use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Model;

class ByObjectFilter extends ApiFilter
{
	const OBJECT_TYPES = ['slides', 'qna', 'quiz', 'chat-message'];

	public function handle($builder)
	{
		$objectType = $this->params['object_type'];
		return $builder->whereHas('tags', function ($query) {
			$query->whereIn('tags.id', $this->params);
		});
	}

	public function count($builder)
	{
		$ids = $builder
			->get(['id'])
			->pluck('id')
			->toArray();

		$matchAll = true;

		$result = $model::searchRaw(
			$this->elasticCraziness($ids, $matchAll)
		);

		return $result;
	}

	protected function elasticCraziness($ids, $matchAll)
	{
		if ($ids === [] && $matchAll) {
			$query = [
				'match_all' => new \stdClass(),
			];
		} else {
			$query = [
				'terms' => ['id' => $ids],
			];
		}

		$size = count($ids);

		return [
			'body' => [
				'size'  => $size,
				'query' => $query,
				'aggs'  => [
					'type' => [
						'terms' => [
							'field'   => 'data.objects.type',
							'size' => count(self::OBJECT_TYPES)
						],
					],
				],
			],
		];
	}
}
