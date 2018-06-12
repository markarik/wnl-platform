<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
    
    private $local;
    
    private $s3;

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
        $this->local = \Storage::disk('local');
        $this->s3 = \Storage::disk('s3');

        $this->scan('/');
        
        return 42;
    }

    private function scan($path) {
		foreach ($this->local->listContents($path) as $content) {
			if ($content['type'] === 'dir') {
				$this->scan($content['path']); continue;
			}

			if($content['type'] === 'file') {
				$this->move($content['path']); continue;
			}

			$this->warn('Unsupported content type: ' . $content['type']);
			continue;
		}
	}

	private	function move($path) {
		$this->s3->put($path, $this->local->get($path));
		$this->line('Moved ' . $path);
	}
}
