<?php

namespace App\Jobs;

use App\Mail\Certificate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class SendCertificate implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	public $user, $file, $type;

	/**
	 * Create a new job instance.
	 *
	 * @param User $user
	 * @param string $file
	 * @param string $type
	 */
	public function __construct(User $user, string $file, string $type)
	{
		$this->user = $user;
		$this->file = $file;
		$this->type = $type;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		Mail::to($this->user)->send(
			new Certificate($this->file, $this->user, $this->type)
		);
	}
}
