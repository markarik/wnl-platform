<?php namespace App\Http\Controllers\Api\Filters\Quiz;

use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\Reactable;
use Carbon\Carbon;
use Auth;

class CollectionFilter extends ApiFilter
{
	protected $expected = ['user_id'];

	public function handle($builder)
	{
		return $this->collection($builder);
	}

	public function count($builder)
	{
		$items = [];
		$this->params['user_id'] = Auth::user()->id;

		$count = $this->collection($builder)->count();

		return [
			'items'   => [
				'collection' =>
					[
						'value' => 'collection',
						'count' => $count,
					],
			],
			'message' => 'collection',
			'type'    => 'list',
			'is_user_specific' => true
		];
	}

	protected function collection($builder)
	{
		$ids = Reactable::select('reactable_id')
			->where('user_id', $this->params['user_id'])
			->where('reactable_type', 'App\\Models\\QuizQuestion')
			->where('reaction_id', 4)
			->get()
			->pluck('reactable_id')
			->toArray();

		return $builder->whereIn('id', $ids);
	}
}
