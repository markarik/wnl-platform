<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DetachReactions implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable;
	/**
	 * @var Model $model
	 */
	private $model;

	/**
	 * Create a new job instance.
	 *
	 * @param Model $model
	 */
	public function __construct($model)
	{
		$this->model = $model;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->model->reactions()->detach();
	}
}
