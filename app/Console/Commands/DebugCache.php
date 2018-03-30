<?php

namespace App\Console\Commands;

use Cache;
use App\Models\Slideshow;
use Illuminate\Console\Command;


class DebugCache extends Command
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cache:debug {tags} {key}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get the key value from cache for given tags';

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
		$cacheTags = explode(',', $this->argument('tags'));
		$key = $this->argument('key');

		print PHP_EOL;
		print 'Has Stored? ' . Cache::tags($cacheTags)->has($key) . PHP_EOL;
		print 'Stored value:' . PHP_EOL;
		dump(Cache::tags($cacheTags)->get($key));
		print PHP_EOL;
	}
}
