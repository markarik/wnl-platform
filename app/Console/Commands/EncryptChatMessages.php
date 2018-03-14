<?php

namespace App\Console\Commands;

use App\Models\ChatMessage;
use Illuminate\Console\Command;

class EncryptChatMessages extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'chatMessages:encrypt';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Encrypt chat messages';

	/**
	 * Create a new command instance.
	 *
	 * @return void
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
		$chatMessages = ChatMessage::all();

		foreach ($chatMessages as $chatMessage) {
			$chatMessage->content = $chatMessage->getOriginal('content');;
			$chatMessage->save();
		}

		$this->comment(PHP_EOL . "OK" . PHP_EOL);
	}
}
