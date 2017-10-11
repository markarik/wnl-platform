<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Comment;
use App\Http\Controllers\Api\ApiTransformer;
use App\Traits\EventContextTrait;

class CommentTransformer extends ApiTransformer
{

	use EventContextTrait;
	protected $parent;

	protected $availableIncludes = ['profiles'];

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}


	public function transform(Comment $comment)
	{
		$data = [
			'id'               => $comment->id,
			'text'             => $comment->text,
			'commentable_id'   => $comment->commentable_id,
			'commentable_type' => $comment->commentable_type,
			'created_at'       => $comment->created_at->timestamp,
			'updated_at'       => $comment->updated_at->timestamp,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		if (self::shouldInclude('context')) {
			$context = $this->addEventContext($comment->commentable);
			$data = array_merge($data, compact('context'));
		}
		
		return $data;
	}

	public function includeProfiles(Comment $comment)
	{
		$profile = $comment->user->profile;

		return $this->item($profile, new UserProfileTransformer(['comments' => $comment->id]), 'profiles');
	}
}
