<?php

namespace App\Console\Commands;

use Cache;
use Lib\Cache\Ping;
use App\Models\Slideshow;
use Illuminate\Console\Command;


class WarmUpCache extends Command
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cache:warmup';

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
	public function handle(Ping $pinger)
	{
		Cache::tags('lessons')->flush();

		$pinger->ping('/papi/v1/editions/1?include=groups.lessons.screens.sections');
	}
}
