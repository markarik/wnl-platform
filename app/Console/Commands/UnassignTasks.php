<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;

class UnassignTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unassign:tasks {moderatorId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unassign moderators tasks';

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
		$moderatorId = $this->argument('moderatorId');
		$moderatorTasks = Task::where('assignee_id', $moderatorId)->update(['assignee_id' => null]);

		print 'Unassigned '.$moderatorTasks.' tasks'; 
	}
}
