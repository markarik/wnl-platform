<?php

namespace App\Console\Commands;

use App\Models\ChatRoom;
use Illuminate\Console\Command;

class MigrateChatRooms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:migrate-rooms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign chat room types basing on their name.';

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
        $rooms = ChatRoom::all();

		foreach ($rooms as $room) {
			if (!str_is($room->name, 'private-*')) {
				$room->type = 'public';
				$room->save();
			}
        }

        return;
    }
}
