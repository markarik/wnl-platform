<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisDumpKeys extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'redis:dumpKeys 
										{pattern}  
										{filename=redis_key_dump}
										{--db=default}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Save selected redis key-val pairs to json file';

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$pattern = $this->argument('pattern');
		$db = $this->option('db');
		$filename = $this->argument('filename');

		$redis = Redis::connection($db);

		$keys = $redis->keys($pattern);
		$keyCount = count($keys);
		$this->info(
			"Found {$keyCount} keys in {$db} database. "
			. "(Provide --db option to use another database.)
		");

		if ($keyCount === 0) exit;

		$data = [];

		foreach ($keys as $key) {
			$data[$key] = $redis->get($key);
		}

		$path = 'exports/' . $filename . '.json';
		\Storage::put($path, json_encode($data));

		$this->info('Done. Saved to ' . $path);

		return;
	}
}
