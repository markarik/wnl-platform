<?php

namespace App\Console\Commands;

use App\Exceptions\ParseErrorException;
use App\Models\StudyBuddy;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\TableStyle;

class StudyBuddyList extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sb:list {status?}';

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
		$status = $this->argument('status') ?? 'awaiting-refund';

		if ($status === 'all') {
			$studyBuddies = StudyBuddy::all();
		} else {
			$studyBuddies = StudyBuddy::where('status', $status)->get();
		}

		$headers = ['user_id', 'order_id', 'code', 'status'];
		$rows = [];

		foreach ($studyBuddies as $studyBuddy) {
			$rows[] = [
				$studyBuddy->order->user->id,
				$studyBuddy->order_id,
				$studyBuddy->code,
				$studyBuddy->status,
			];
		}

		$this->table($headers, $rows);

		return;
	}
}
