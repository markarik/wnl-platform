<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteModels implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;
	/**
	 * @var
	 */
	private $models;

	/**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($models)
    {
		$this->models = $models;
	}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	if ($this->models->count() <= 0) return;

		foreach ($this->models as $model) {
			$model->delete();
		}
    }
}
