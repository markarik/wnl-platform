<?php namespace App\Console\Commands;

use Storage;
use Lib\SlideParser\Parser;
use Illuminate\Console\Command;


class SlidesImport extends Command
{
	const DIRECTORY = 'slideshows';

	protected $parser;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slides:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import slideshows to database from storage.';

	/**
	 * Create a new command instance.
	 *
	 * @param Parser $parser
	 */
	public function __construct(Parser $parser)
	{
		parent::__construct();

		$this->parser = $parser;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$files = Storage::disk('s3')->files(self::DIRECTORY);
		$this->info('Importing slideshows...');
		$bar = $this->output->createProgressBar(count($files));
		foreach ($files as $file) {
			$this->importFile($file);
			$bar->advance();
			\Log::debug($file . ' processed');
		}
		print PHP_EOL;

		return;
	}

	/**
	 * Import slideshow form file.
	 *
	 * @param $file
	 */
	public function importFile($file)
	{
		$contents = Storage::disk('s3')->get($file);
		$this->parser->parse($contents);
	}
}
