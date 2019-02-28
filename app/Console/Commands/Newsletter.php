<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;

class Newsletter extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'newsletter 
    						{--R|recipient=newsletter.test@wiecejnizlek.pl}
    						{--T|template=example-newsletter}
    						{--S|subject=Test}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$template = $this->option('template');
		$subject = $this->option('subject');
		$recipient = $this->option('recipient');

		$this->warn("Env: " . env('APP_ENV'));
		$this->info("Your current mail settings:\n");
		dump(config('mail'));

		if (
		!$this->confirm("Send email using template '{$template}' to '{$recipient}' with subject '{$subject}'?")
		) exit;

		Mail::send(
			new \App\Mail\Newsletter($template, $subject, $recipient)
		);

		$this->info('✈');
	}
}
