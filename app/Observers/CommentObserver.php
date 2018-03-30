<?php

namespace App\Observers;

use App\Models\Comment;
use App\Jobs\DeleteModels;
use App\Jobs\DetachReactions;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CommentObserver
{
	use DispatchesJobs;


	public function created(Comment $comment)
	{
		//
	}

	public function deleted(Comment $comment)
	{
		$this->dispatch(new DetachReactions($comment));
	}
}
