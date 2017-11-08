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
	public $user;
	public $file;

	/**
	 * Create a new job instance.
	 *
	 * @param User $user
	 * @param string $file
	 */
	public function __construct(User $user, string $file)
	{
		$this->user = $user;
		$this->file = $file;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		Mail::to($this->user)->send(
			new Certificate($this->file, $this->user)
		);
	}
}
