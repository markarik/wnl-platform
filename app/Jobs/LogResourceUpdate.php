<?php

namespace App\Jobs;

use App\Models\Invoice as InvoiceModel;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ResourceChangelog;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Lib\Invoice\Invoice as InvoiceGenerator;
use App\Mail\PaymentConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LogResourceUpdate implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	const TRACKED_PROPERTIES = [
		'verified_at',
		'deleted_at'
	];

	/** @var Model $model */
	private $model;

	/** @var $userId */
	private $userId;

	/**
	 * Create a new job instance.
	 *
	 * @param Model $model
	 */
	public function __construct(Model $model, $userId)
	{
		$this->model = $model;
		$this->userId = $userId;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		foreach (self::TRACKED_PROPERTIES as $property) {
			if ($this->model->isDirty($property)) {
				ResourceChangelog::create([
					'user_id' => $this->userId,
					'resource_type' => get_class($this->model),
					'resource_id' => $this->model->id,
					'property' => $property,
					'value' => $this->model[$property]
				]);
			}
		}
	}
}
