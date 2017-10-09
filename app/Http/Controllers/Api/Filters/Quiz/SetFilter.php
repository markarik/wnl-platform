<?php namespace App\Http\Controllers\Api\Filters\Quiz;

use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\QuizSet;
use Auth;

class SetFilter extends ApiFilter
{
	protected $expected = ['quiz_set_id'];

	public function handle($builder)
	{
		return $this->collection($builder);
	}

	public function count($builder)
	{
		$items = [];
        $count = $this->collection($builder)->count();
        Auth::user()->id;

		return [
			'items'   => [
				[
					'value' => 'collection',
					'count' => $count,
				],
			],
			'message' => 'collection',
			'type'    => 'list',
		];
	}

	protected function collection($builder)
	{
        $ids = QuizSet::find($this->params['quiz_set_id'])
            ->questions()
			->pluck('quiz_question_id')
			->toArray();

		return $builder->whereIn('id', $ids);
	}
}
