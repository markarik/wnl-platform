<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;

class DeleteModels implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable;
	/**
	 * @var Collection
	 */
	private $models;

	/**
	 * Create a new job instance.
	 *
	 * @param Collection $models
	 */
	public function __construct($models)
	{
		$this->models = $models;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function handle()
	{
		if ($this->models->count() <= 0) return;

		foreach ($this->models as $model) {
			$model->delete();
		}
	}
}
