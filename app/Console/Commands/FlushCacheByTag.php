<?php

namespace App\Console\Commands;

use Cache;
use Illuminate\Console\Command;

class FlushCacheByTag extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cache:tag {tag}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Flush cache entries having given tag.';

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
		foreach (explode(',', $this->argument('tag')) as $tag) {
			Cache::tags($tag)->flush();
		}
		$this->info('OK.');

		return true;
	}
}
