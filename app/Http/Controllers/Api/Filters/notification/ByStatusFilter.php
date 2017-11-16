<?php namespace App\Http\Controllers\Api\Filters\Notification;


use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Model;

class ByStatusFilter extends ApiFilter
{
	const OBJECT_TYPES = ['slides', 'qna', 'quiz', 'chat-message'];

	public function handle($builder)
	{
		if ($this->params['read'] === true) {
			return $builder->whereNull('read_at');
		} else {
			return $builder->whereNotNull('readAt');
		}
	}

	public function count($builder)
	{
			$unreadCount = $builder->whereNull('read_at')->count();
			$readCount = $builder->whereNotNull('readAt')->count;

		return [
			'read' => $readCount,
			'unread' => $unreadCount
		];
	}
}
