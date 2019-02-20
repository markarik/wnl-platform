<?php

namespace App\Jobs;

use App\Models\Contracts\WithReactions;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DetachReactions implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable;
	/**
	 * @var WithReactions $model
	 */
	private $model;

	/**
	 * Create a new job instance.
	 *
	 * @param WithReactions $model
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
