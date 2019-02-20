<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Laravel\Scout\Searchable;

class SearchImportAll implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public $model;

	/**
	 * Create a new job instance.
	 *
	 * @param string $model
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
		/** @var Searchable $model */
		$model = $this->model;
		$model::makeAllSearchable();
	}
}
