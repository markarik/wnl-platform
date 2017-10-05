<?php


namespace App\Observers;


use App\Events\CommentPosted;
use App\Models\Comment;
use Illuminate\Foundation\Bus\DispatchesJobs;


class CommentObserver
{
	use DispatchesJobs;


	public function created(Comment $comment)
	{
		//
	}
}
