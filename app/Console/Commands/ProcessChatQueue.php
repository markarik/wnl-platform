<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessChatQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:process-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume messages queue with chat events';

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
		\Rabbit::consume('chat-events', function ($message, $resolver) {
			dump($message->body);

			$resolver->acknowledge($message);
		});
    }
}
