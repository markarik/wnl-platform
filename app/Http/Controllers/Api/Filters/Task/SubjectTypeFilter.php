<?php namespace App\Http\Controllers\Api\Filters\Task;


use App\Http\Controllers\Api\Filters\ApiFilter;
use Auth;

class SubjectTypeFilter extends ApiFilter
{
	protected $expected = ['user_id', 'list'];

	public function handle($builder)
	{
		$builder = $builder->where(function ($query) {
			foreach ($this->params['list'] as $state) {
				$query->orWhere(function ($query) use ($state) {
					$this->{$state}($query);
				});
			}
		});

		return $builder;
	}

	public function values()
	{
		return ['slide', 'qna_question', 'qna_answer', 'quiz_question'];
	}

	public function count($builder)
	{
		return 0;
	}

	protected function slide($query)
	{
        return $query->where('subject_type', 'slide');
	}

	protected function qna_question($query)
	{
		return $query->where('subject_type', 'qna_question');
	}

	protected function qna_answer($query)
	{
		return $query->where('subject_type', 'qna_answer');
	}

	protected function quiz_question($query)
	{
		$query->where('subject_type', 'quiz_question');
	}
}
