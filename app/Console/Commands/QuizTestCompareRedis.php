<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class QuizTestCompareRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quizTest:compareRedis';

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
		$byProfile = json_decode(\Storage::drive()->get("quiz_test_dump_before.json"));
		$byUser = collect(json_decode(\Storage::drive()->get("quiz_test_dump_after.json")));

		foreach ($byProfile as $record) {
			list ($userId, $quizId, $score) = $record;
			$dupa = $byUser->filter(function($e) use ($userId, $quizId) {
				return $e[0] === $userId && $e[1] === $quizId;
			})->first();

			print "$userId $quizId $score {$dupa[2]} ";
			if ($score !== $dupa[2]) {
				print 'Whoooooops';
			} else {
				print 'OK';
			}
			print PHP_EOL;
		}
    }
}
