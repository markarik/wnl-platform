<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;

class MigrateFilesToS3 extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 's3:migrate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Move all files from local disk to S3';

	/**
	 * Create a new command instance.
	 *
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
		$local = \Storage::disk('local');
		$s3 = \Storage::disk('s3');

		$this->scan($local, '/', function ($path) use ($s3, $local) {
			$this->info('Copying ' . $path);
			if (!$s3->exists($path)) {
				$s3->put($path, $local->get($path));
				$this->line('Copied ' . $path);
			}
		});


		$this->scan($s3, '/public', function ($path) use ($s3) {
			$s3->setVisibility($path, 'public');
			$this->line('Made public ' . $path);
		});

		return 42;
	}

	/**
	 * Recursively traverse directory tree on $disk starting from $path
	 * and execute $callback for each file.
	 *
	 * @param FilesystemAdapter|Filesystem $disk
	 * @param string $path
	 * @param callable $callback
	 */
	private function scan($disk, $path, $callback)
	{
		foreach ($disk->listContents($path) as $content) {
			if ($content['type'] === 'dir') {
				$this->scan($disk, $content['path'], $callback);
				continue;
			}

			if ($content['type'] === 'file') {
				$callback($content['path']);
				continue;
			}

			$this->warn('Unsupported content type: ' . $content['type']);
			continue;
		}
	}
}
