<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class QuizTestCompareMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quizTest:compareMysql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
		$byProfile = json_decode(\Storage::drive()->get("quiz_test_dump_mysql_before.json"), true);
		$byUser = json_decode(\Storage::drive()->get("quiz_test_dump_mysql_after.json"), true);

		foreach ($byProfile as $id => $count) {
			if ($count !== $byUser[$id]) {
				print 'Whoooooops';
			} else {
				print 'OK';
			}
			print PHP_EOL;
		}
    }
}
