<?php namespace App\Observers;

use Cache;
use App\Models\Comment;
use App\Events\CommentPosted;
use Illuminate\Foundation\Bus\DispatchesJobs;


class CachedModelObserver
{
	use DispatchesJobs;


	public function created($model)
	{
		$this->bust($model);
	}

	public function updated($model)
	{
		$this->bust($model);
	}

	public function deleted($model)
	{
		$this->bust($model);
	}

	public function bust($model)
	{
		$resource = str_plural(snake_case(class_basename($model)));
		Cache::tags($resource)->flush();
	}
}
