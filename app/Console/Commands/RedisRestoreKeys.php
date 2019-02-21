<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisRestoreKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:restoreKeys
										{filename=redis_key_dump}
										{--db=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore redis key-val pairs from json file';

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
		$db = $this->option('db');
		$filename = $this->argument('filename');

		$path = 'import/' . $filename . '.json';
		$redis = Redis::connection($db);

		$keys = json_decode(\Storage::drive()->get($path), true);
		$keyCount = count($keys);
		$this->info("Restore {$keyCount} keys from {$path} to {$db} database?");

		if (!$this->confirm('')) exit;

		foreach ($keys as $key => $value) {
			$redis->set($key, $value);
		}

		$this->info('Done.');

		return;
    }
}
