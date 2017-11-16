<?php namespace App\Http\Controllers\Api\Filters\Notification;


use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Model;

class ByUserFilter extends ApiFilter
{
	const OBJECT_TYPES = ['slides', 'qna', 'quiz', 'chat-message'];

	public function handle($builder)
	{
		return $builder->where('notifiable_id', $this->params['user_id']);
	}

	public function count($builder)
	{
		return $builder->where('notifiable_id', $this->params['user_id'])->count();
	}

}
